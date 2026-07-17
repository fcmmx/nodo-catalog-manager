<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Services\Social\Exceptions\SocialPublishException;
use App\Services\Social\SocialPublisher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SocialPostController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('ver redes');

        $month = $request->filled('mes') ? Carbon::parse($request->string('mes')) : now();

        $posts = SocialPost::with(['account', 'product'])
            ->when($request->channel, fn ($q) => $q->where('channel', $request->channel))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->view !== 'todas', function ($q) use ($month) {
                $q->whereBetween('scheduled_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                    ->orWhereNull('scheduled_at');
            })
            ->orderBy('scheduled_at')
            ->get();

        return view('social.posts.index', [
            'posts' => $posts,
            'month' => $month,
            'channels' => SocialPost::CHANNELS,
            'statuses' => SocialPost::STATUSES,
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('crear redes');

        return view('social.posts.form', [
            'post' => new SocialPost,
            'accounts' => SocialAccount::where('is_active', true)->get(),
            'products' => Product::orderBy('name')->limit(500)->get(['id', 'name', 'short_description', 'main_image', 'url']),
            'selectedProduct' => $request->integer('product_id') ? Product::find($request->integer('product_id')) : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('crear redes');

        $data = $this->validated($request);
        $data['user_id'] = $request->user()->id;
        $data['status'] = $data['scheduled_at'] ? 'programada' : 'borrador';

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('social', 'public');
        }

        $post = SocialPost::create($data);

        activity('redes')->causedBy($request->user())->log('Creó una publicación para '.$post->channel);

        return redirect()->route('social.posts.edit', $post)->with('success', 'Publicación creada correctamente.');
    }

    public function edit(SocialPost $post): View
    {
        $this->authorize('editar redes');

        return view('social.posts.form', [
            'post' => $post,
            'accounts' => SocialAccount::where('is_active', true)->get(),
            'products' => Product::orderBy('name')->limit(500)->get(['id', 'name', 'short_description', 'main_image', 'url']),
            'selectedProduct' => $post->product,
        ]);
    }

    public function update(Request $request, SocialPost $post): RedirectResponse
    {
        $this->authorize('editar redes');

        $data = $this->validated($request);

        if (! in_array($post->status, ['enviada', 'publicada_manual'])) {
            $data['status'] = $data['scheduled_at'] ? 'programada' : 'borrador';
        }

        if ($request->hasFile('image')) {
            if ($post->image_path) {
                Storage::disk('public')->delete($post->image_path);
            }
            $data['image_path'] = $request->file('image')->store('social', 'public');
        }

        $post->update($data);

        return redirect()->route('social.posts.edit', $post)->with('success', 'Publicación actualizada correctamente.');
    }

    public function destroy(SocialPost $post): RedirectResponse
    {
        $this->authorize('eliminar redes');

        $post->delete();

        return back()->with('success', 'Publicación eliminada.');
    }

    public function duplicate(Request $request, SocialPost $post): RedirectResponse
    {
        $this->authorize('crear redes');

        $data = $request->validate(['channel' => ['required', 'in:'.implode(',', SocialPost::CHANNELS)]]);

        $copy = $post->replicate(['status', 'external_post_id', 'result', 'error_message']);
        $copy->channel = $data['channel'];
        $copy->status = 'borrador';
        $copy->social_account_id = null;
        $copy->duplicated_from = $post->id;
        $copy->save();

        return redirect()->route('social.posts.edit', $copy)->with('success', 'Publicación duplicada para '.$data['channel'].'. Ajusta el texto si es necesario.');
    }

    public function approve(SocialPost $post): RedirectResponse
    {
        $this->authorize('aprobar redes');

        $post->update(['status' => $post->scheduled_at ? 'programada' : 'borrador']);

        return back()->with('success', 'Publicación aprobada.');
    }

    public function cancel(SocialPost $post): RedirectResponse
    {
        $this->authorize('editar redes');

        $post->update(['status' => 'cancelada']);

        return back()->with('success', 'Publicación cancelada.');
    }

    public function publishNow(SocialPost $post, SocialPublisher $publisher): RedirectResponse
    {
        $this->authorize('publicar redes');

        try {
            $publisher->publish($post);

            return back()->with('success', 'Publicación enviada correctamente.');
        } catch (SocialPublishException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function markPublishedManually(SocialPost $post): RedirectResponse
    {
        $this->authorize('publicar redes');

        $post->update(['status' => 'publicada_manual', 'result' => 'Marcada como publicada manualmente']);

        activity('redes')->log('Marcó como publicada manualmente la publicación #'.$post->id);

        return back()->with('success', 'Publicación marcada como publicada manualmente.');
    }

    public function downloadImage(SocialPost $post): StreamedResponse
    {
        $this->authorize('ver redes');

        if (! $post->image_path || ! Storage::disk('public')->exists($post->image_path)) {
            abort(404, 'Esta publicación no tiene una imagen.');
        }

        return Storage::disk('public')->download($post->image_path);
    }

    public function exportCalendar(Request $request): StreamedResponse
    {
        $this->authorize('ver redes');

        $posts = SocialPost::with('account')->whereNotNull('scheduled_at')->orderBy('scheduled_at')->get();

        return response()->streamDownload(function () use ($posts) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['fecha', 'hora', 'canal', 'cuenta', 'estado', 'contenido', 'enlace']);
            foreach ($posts as $post) {
                fputcsv($handle, [
                    $post->scheduled_at->format('Y-m-d'),
                    $post->scheduled_at->format('H:i'),
                    $post->channel,
                    $post->account?->label,
                    SocialPost::STATUSES[$post->status] ?? $post->status,
                    str_replace(["\r", "\n"], ' ', $post->content),
                    $post->link,
                ]);
            }
            fclose($handle);
        }, 'calendario-editorial-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv']);
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'product_id' => ['nullable', 'exists:products,id'],
            'social_account_id' => ['nullable', 'exists:social_accounts,id'],
            'channel' => ['required', 'in:'.implode(',', SocialPost::CHANNELS)],
            'content' => ['required', 'string', 'max:5000'],
            'hashtags' => ['nullable', 'string', 'max:500'],
            'link' => ['nullable', 'url', 'max:500'],
            'scheduled_at' => ['nullable', 'date'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:8192'],
        ]);

        $data['timezone'] = $data['timezone'] ?? 'America/Mexico_City';
        $data['scheduled_at'] = $data['scheduled_at'] ?? null;

        return $data;
    }
}
