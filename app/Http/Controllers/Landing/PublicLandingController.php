<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\LandingLead;
use App\Models\LandingPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicLandingController extends Controller
{
    public function show(string $slug): View
    {
        $landing = LandingPage::where('slug', $slug)->where('status', 'publicada')->with('product')->firstOrFail();

        $landing->increment('views_count');

        return view('landing.public', ['landing' => $landing]);
    }

    public function captureLead(Request $request, string $slug): RedirectResponse
    {
        $landing = LandingPage::where('slug', $slug)->where('status', 'publicada')->firstOrFail();

        if (! $landing->capture_form_enabled) {
            abort(404);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $contact = null;
        if ($landing->contact_list_id) {
            $contact = Contact::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'] ?? null,
                    'source' => 'landing',
                    'consent' => true,
                    'consent_at' => now(),
                    'subscribed' => true,
                ]
            );
            $contact->lists()->syncWithoutDetaching([$landing->contact_list_id]);
        }

        LandingLead::create([
            'landing_page_id' => $landing->id,
            'contact_id' => $contact?->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'] ?? null,
            'utm_source' => $request->query('utm_source'),
            'utm_medium' => $request->query('utm_medium'),
            'utm_campaign' => $request->query('utm_campaign'),
            'ip_address' => $request->ip(),
        ]);

        $landing->increment('leads_count');

        return back()->with('success', '¡Gracias! Te contactaremos muy pronto.');
    }
}
