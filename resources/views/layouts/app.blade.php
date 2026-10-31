<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>{{ $title ?? 'Ryo Digital Website — Website dan Pengalaman Digital untuk Bisnis' }}</title>
    <meta name="description" content="{{ $description ?? 'Ryo Digital Website membantu UMKM, perusahaan, lembaga pendidikan, dan personal brand membangun website yang jelas, berkarakter, dan siap berkembang.' }}">
    <link rel="canonical" href="{{ $canonical ?? rtrim(config('app.url'), '/').'/'.ltrim(request()->getPathInfo(), '/') }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:type" content="{{ $ogType ?? 'website' }}"><meta property="og:title" content="{{ $title ?? 'Ryo Digital Website' }}"><meta property="og:description" content="{{ $description ?? 'Website dan pengalaman digital yang membantu bisnis melangkah maju.' }}">
    @if(!empty($ogImage))<meta property="og:image" content="{{ $ogImage }}">@endif
    <meta name="twitter:card" content="summary_large_image"><meta name="twitter:title" content="{{ $title ?? 'Ryo Digital Website' }}"><meta name="twitter:description" content="{{ $description ?? 'Website dan pengalaman digital yang membantu bisnis melangkah maju.' }}">@if(!empty($ogImage))<meta name="twitter:image" content="{{ $ogImage }}">@endif
    <script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'Organization', 'name' => config('app.name'), 'url' => config('app.url'), 'telephone' => '+6285648983077', 'sameAs' => array_values(config('ryo.social'))], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    @stack('structured-data')
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
<header class="site-header fixed inset-x-0 top-0 z-40 border-b border-white/10 bg-[#11151B]/90 backdrop-blur-md">
    <nav class="site-nav wrap flex h-[76px] items-center justify-between gap-4" aria-label="Main navigation">
        <a href="{{ route('home') }}" class="site-brand flex shrink-0 items-center gap-3 text-white" aria-label="Ryo Digital Website home">
            <span class="site-brand-mark flex h-9 w-9 items-center justify-center border border-emerald-400/60 font-display text-sm font-bold text-emerald-400">RD</span>
            <span class="type-micro whitespace-nowrap font-extrabold tracking-[.1em]">RYO<span class="text-emerald-400">.</span>DIGITAL<span class="hidden sm:inline"> WEBSITE</span></span>
        </a>

        <div class="site-nav-links hidden items-center gap-6 xl:gap-8 lg:flex">
            <a class="nav-link" href="{{ route('portfolio') }}">Karya</a>
            <a class="nav-link" href="{{ route('services') }}">Layanan</a>
            <a class="nav-link" href="{{ route('regions.index') }}">Wilayah</a>
            <a class="nav-link" href="{{ route('pricing') }}">Harga</a>
            <a class="nav-link" href="{{ route('about') }}">Tentang</a>
            <a class="nav-link" href="{{ route('contact') }}">Kontak</a>
        </div>

        <a class="site-nav-cta btn btn-primary hidden !min-h-[44px] !px-5 whitespace-nowrap lg:inline-flex" href="{{ route('contact') }}">Mulai proyek <x-arrow-icon/></a>

        <button type="button" class="site-menu-toggle flex h-11 w-11 shrink-0 items-center justify-center border border-white/15 text-white lg:hidden" aria-expanded="false" aria-controls="site-menu" aria-label="Toggle menu">
            <svg class="icon-open h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="1.6" d="M4 7h16M4 12h16M4 17h16"/></svg>
            <svg class="icon-close hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="1.6" d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
    </nav>

    <div id="site-menu" class="mobile-menu site-menu-panel hidden lg:hidden absolute inset-x-0 top-full max-h-[calc(100dvh-76px)] overflow-y-auto border-b border-white/10 bg-[#11151B]/95 backdrop-blur-md">
        <div class="site-menu-links wrap flex flex-col py-3">
            <a class="nav-link flex min-h-[48px] items-center border-b border-white/5" href="{{ route('portfolio') }}">Karya</a>
            <a class="nav-link flex min-h-[48px] items-center border-b border-white/5" href="{{ route('services') }}">Layanan</a>
            <a class="nav-link flex min-h-[48px] items-center border-b border-white/5" href="{{ route('regions.index') }}">Wilayah</a>
            <a class="nav-link flex min-h-[48px] items-center border-b border-white/5" href="{{ route('pricing') }}">Harga</a>
            <a class="nav-link flex min-h-[48px] items-center border-b border-white/5" href="{{ route('about') }}">Tentang</a>
            <a class="nav-link flex min-h-[48px] items-center" href="{{ route('contact') }}">Kontak</a>
            <a class="btn btn-primary mt-4 w-full justify-center" href="{{ route('contact') }}">Mulai proyek <x-arrow-icon/></a>
        </div>
    </div>
</header>
<main>@yield('content')</main>
<footer class="border-t border-white/10 bg-[#0D1116]">
    <div class="wrap grid gap-12 py-14 md:grid-cols-[1.3fr_.7fr_.7fr]">
        <div><a href="{{ route('home') }}" class="type-micro font-extrabold tracking-[.1em]">RYO<span class="text-emerald-400">.</span>DIGITAL WEBSITE</a><p class="mt-4 type-body max-w-sm leading-7 text-[#A5ADB5]">Partner digital untuk membangun website yang jelas, berkarakter, dan membantu bisnismu melangkah maju.</p></div>
        <div><p class="eyebrow">Jelajahi</p><div class="mt-5 flex flex-col gap-3 type-small text-[#A5ADB5]"><a href="{{ route('portfolio') }}">Karya</a><a href="{{ route('services') }}">Layanan</a><a href="{{ route('regions.index') }}">Wilayah</a><a href="{{ route('pricing') }}">Harga</a><a href="{{ route('about') }}">Tentang</a><a href="{{ route('blog') }}">Jurnal</a></div></div>
        <div><p class="eyebrow">Hubungi</p><div class="mt-5 flex flex-col gap-3 type-small text-[#A5ADB5]"><a href="https://wa.me/6285648983077" target="_blank" rel="noreferrer">WhatsApp</a>@if($siteSettings['email'] ?? config('ryo.email'))<a href="mailto:{{ $siteSettings['email'] ?? config('ryo.email') }}">{{ $siteSettings['email'] ?? config('ryo.email') }}</a>@endif @foreach(config('ryo.social') as $network => $url)@php($configuredUrl=$siteSettings[strtolower($network)] ?? $url)@if($configuredUrl)<a href="{{ $configuredUrl }}" target="_blank" rel="noreferrer">{{ $network }}</a>@endif @endforeach</div></div>
    </div>
    <div class="wrap flex flex-col justify-between gap-3 border-t border-white/10 py-5 type-micro tracking-[.12em] text-[#929CA6] sm:flex-row"><span>© {{ date('Y') }} Ryo Digital Website. Hak cipta dilindungi.</span><span>DIGITAL DENGAN ARAH.</span></div>
</footer>

<script>
(function () {
    var btn = document.querySelector('.site-menu-toggle');
    var menu = document.getElementById('site-menu');
    if (!btn || !menu) return;
    function set(open) {
        menu.classList.toggle('hidden', !open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        btn.querySelector('.icon-open').classList.toggle('hidden', open);
        btn.querySelector('.icon-close').classList.toggle('hidden', !open);
    }
    btn.addEventListener('click', function () {
        set(btn.getAttribute('aria-expanded') !== 'true');
    });
    menu.addEventListener('click', function (e) { if (e.target.closest('a')) set(false); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') set(false); });
    window.matchMedia('(min-width:1024px)').addEventListener('change', function (e) { if (e.matches) set(false); });
})();
</script>
</body></html>
