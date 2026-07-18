@php
    use App\Models\Setting;

    $companyName = Setting::get('company_name', 'NODO 360 MARKETING TECHNOLOGY');
    $companyWhatsapp = Setting::get('company_whatsapp');
    $companyWebsite = Setting::get('company_website');
    $primaryColor = Setting::get('primary_color', '#2563EB');

    $pageTitle = $landing->meta_title ?: $landing->headline;
    $pageDescription = $landing->meta_description ?: $landing->subheadline ?: $landing->headline;
    $ogImage = $landing->ogImageUrl();

    $whatsappUrl = null;
    if ($landing->cta_whatsapp_number) {
        $digits = preg_replace('/\D/', '', $landing->cta_whatsapp_number);
        $whatsappUrl = 'https://wa.me/'.$digits.($landing->cta_whatsapp_message ? '?text='.rawurlencode($landing->cta_whatsapp_message) : '');
    }
    $primaryCtaUrl = $whatsappUrl ?: $landing->cta_url;

    $structuredData = $landing->structured_data ?: [];
    if (empty($structuredData)) {
        $structuredData = [
            '@context' => 'https://schema.org',
            '@graph' => array_values(array_filter([
                [
                    '@type' => 'Organization',
                    'name' => $companyName,
                    'url' => $companyWebsite ?: url('/'),
                ],
                [
                    '@type' => 'WebPage',
                    'name' => $pageTitle,
                    'description' => $pageDescription,
                    'url' => $landing->publicUrl(),
                ],
                $landing->product ? [
                    '@type' => 'Product',
                    'name' => $landing->product->name,
                    'description' => $landing->product->short_description,
                    'offers' => $landing->product->price !== null ? [
                        '@type' => 'Offer',
                        'price' => (string) $landing->product->price,
                        'priceCurrency' => $landing->product->currency,
                    ] : null,
                ] : null,
                (function () use ($landing) {
                    $faqSection = collect($landing->sections)->firstWhere('type', 'faq');
                    if (! $faqSection || empty($faqSection['items'])) {
                        return null;
                    }

                    return [
                        '@type' => 'FAQPage',
                        'mainEntity' => collect($faqSection['items'])->filter(fn ($i) => ! empty($i['heading']))->map(fn ($i) => [
                            '@type' => 'Question',
                            'name' => $i['heading'],
                            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $i['text'] ?? ''],
                        ])->values()->all(),
                    ];
                })(),
            ])),
        ];
    }
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ $landing->publicUrl() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $landing->publicUrl() }}">
    @if ($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">

    <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

    <link rel="stylesheet" href="{{ asset('build/app.css') }}?v={{ filemtime(public_path('build/app.css')) }}">

    @if ($landing->gtm_id)
        <script>
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','{{ $landing->gtm_id }}');
        </script>
    @endif
    @if ($landing->ga4_id)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $landing->ga4_id }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $landing->ga4_id }}');
        </script>
    @endif
    @if ($landing->meta_pixel_id)
        <script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
            document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $landing->meta_pixel_id }}');
            fbq('track', 'PageView');
        </script>
    @endif

    <style>:root { --nodo-landing-primary: {{ $primaryColor }}; }</style>
</head>
<body class="min-h-screen bg-white font-sans text-slate-900 antialiased">

    @if ($landing->gtm_id)
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $landing->gtm_id }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif

    <header class="border-b border-slate-100">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
            <span class="text-lg font-bold tracking-tight" style="color: var(--nodo-landing-primary)">{{ $companyName }}</span>
            @if ($primaryCtaUrl)
                <a href="{{ $primaryCtaUrl }}" target="_blank" rel="noopener" class="rounded-lg px-4 py-2 text-sm font-semibold text-white shadow" style="background: var(--nodo-landing-primary)">{{ $landing->cta_text }}</a>
            @endif
        </div>
    </header>

    <section class="relative overflow-hidden bg-gradient-to-br from-slate-50 to-white px-6 py-20">
        <div class="mx-auto grid max-w-5xl grid-cols-1 items-center gap-12 lg:grid-cols-2">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ $landing->headline }}</h1>
                @if ($landing->subheadline)
                    <p class="mt-4 text-lg text-slate-600">{{ $landing->subheadline }}</p>
                @endif
                @if ($primaryCtaUrl)
                    <a href="{{ $primaryCtaUrl }}" target="_blank" rel="noopener" class="mt-8 inline-block rounded-xl px-8 py-4 text-base font-semibold text-white shadow-lg" style="background: var(--nodo-landing-primary)">{{ $landing->cta_text }}</a>
                @elseif ($landing->capture_form_enabled)
                    <a href="#contacto" class="mt-8 inline-block rounded-xl px-8 py-4 text-base font-semibold text-white shadow-lg" style="background: var(--nodo-landing-primary)">{{ $landing->cta_text }}</a>
                @endif
            </div>
            @if ($landing->heroImageUrl())
                <img src="{{ $landing->heroImageUrl() }}" alt="{{ $landing->headline }}" class="w-full rounded-2xl object-cover shadow-xl">
            @endif
        </div>
    </section>

    @foreach ($landing->sections ?? [] as $section)
        @include('landing.sections.'.$section['type'], ['section' => $section, 'landing' => $landing, 'primaryColor' => $primaryColor])
    @endforeach

    @if ($landing->capture_form_enabled)
        <section id="contacto" class="bg-slate-50 px-6 py-20">
            <div class="mx-auto max-w-xl">
                <h2 class="text-center text-2xl font-bold text-slate-900">¿Quieres saber más?</h2>
                <p class="mt-2 text-center text-slate-600">Déjanos tus datos y te contactaremos.</p>

                @if (session('success'))
                    <div class="mt-6 rounded-xl bg-emerald-50 px-4 py-3 text-center text-sm text-emerald-700">{{ session('success') }}</div>
                @else
                    <form method="POST" action="{{ route('landing.lead', array_merge(['slug' => $landing->slug], request()->query())) }}" class="mt-8 space-y-4 rounded-2xl bg-white p-6 shadow-lg">
                        @csrf
                        <div>
                            <input type="text" name="name" required placeholder="Nombre completo" value="{{ old('name') }}" class="w-full rounded-lg border border-slate-200 px-4 py-3 text-sm focus:border-slate-400 focus:outline-none">
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <input type="email" name="email" required placeholder="Correo electrónico" value="{{ old('email') }}" class="w-full rounded-lg border border-slate-200 px-4 py-3 text-sm focus:border-slate-400 focus:outline-none">
                            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <input type="text" name="phone" placeholder="Teléfono (opcional)" value="{{ old('phone') }}" class="w-full rounded-lg border border-slate-200 px-4 py-3 text-sm focus:border-slate-400 focus:outline-none">
                        </div>
                        <div>
                            <textarea name="message" rows="3" placeholder="Cuéntanos qué necesitas (opcional)" class="w-full rounded-lg border border-slate-200 px-4 py-3 text-sm focus:border-slate-400 focus:outline-none">{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="w-full rounded-lg py-3 text-sm font-semibold text-white shadow" style="background: var(--nodo-landing-primary)">Enviar</button>
                    </form>
                @endif
            </div>
        </section>
    @endif

    <footer class="border-t border-slate-100 px-6 py-10 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} {{ $companyName }}
    </footer>
</body>
</html>
