<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaignSend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TrackingController extends Controller
{
    /**
     * Píxel de apertura de 1x1 (transparente). Se referencia desde el
     * correo enviado; si el cliente de correo carga imágenes, marca la
     * apertura. No todos los proveedores de correo permiten esto (Apple
     * Mail Privacy Protection, por ejemplo, precarga imágenes), por lo que
     * la métrica de aperturas es orientativa, como en cualquier plataforma.
     */
    public function pixel(string $token): Response
    {
        $send = EmailCampaignSend::where('token', $token)->first();

        if ($send && ! $send->opened_at) {
            $send->update(['opened_at' => now()]);
            $send->campaign->increment('open_count');
        }

        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBTAA7');

        return response($pixel, 200)->header('Content-Type', 'image/gif');
    }

    public function click(Request $request, string $token): RedirectResponse
    {
        $send = EmailCampaignSend::where('token', $token)->first();
        $target = base64_decode((string) $request->query('url', ''));

        if (! $target || ! filter_var($target, FILTER_VALIDATE_URL)) {
            abort(404);
        }

        if ($send && ! $send->clicked_at) {
            $send->update(['clicked_at' => now()]);
            $send->campaign->increment('click_count');
        }

        return redirect()->away($target);
    }

    public function unsubscribeForm(string $token): View
    {
        $send = EmailCampaignSend::where('token', $token)->with('contact')->firstOrFail();

        return view('email.unsubscribe', ['contact' => $send->contact, 'token' => $token]);
    }

    public function unsubscribe(string $token): View
    {
        $send = EmailCampaignSend::where('token', $token)->with(['contact', 'campaign'])->firstOrFail();
        $send->contact->unsubscribe();
        $send->campaign->increment('unsubscribe_count');

        activity('contactos')->log('El contacto '.$send->contact->email.' se dio de baja.');

        return view('email.unsubscribed', ['contact' => $send->contact]);
    }
}
