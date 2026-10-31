@extends('layouts.app')

@section('content')
<section class="wrap section-space pt-36">
    <nav class="mb-8 text-sm text-[#A1A4AD]" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-emerald-400">Beranda</a>
        <span class="px-2 text-emerald-400">/</span>
        <a href="{{ route('services.show', $serviceSlug) }}" class="hover:text-emerald-400">{{ $service['title'] }}</a>
        <span class="px-2 text-emerald-400">/</span><span>{{ $city['name'] }}</span>
    </nav>
    <p class="eyebrow">Layanan regional · {{ $city['province'] }}</p>
    <h1 class="display mt-6 max-w-5xl text-[clamp(48px,8vw,104px)]">{{ strtoupper($service['title']) }}<br><span class="text-emerald-400">{{ strtoupper($city['name']) }}</span></h1>
    <p class="mt-7 max-w-2xl text-lg leading-8 text-[#A1A4AD]">{{ $service['description'] }} Kami memahami kebutuhan digital bisnis di {{ $city['name'] }} dan membantu menyusun website yang sesuai dengan tujuan serta audiensmu.</p>
    <a class="btn btn-primary mt-8" href="https://wa.me/6285648983077?text={{ rawurlencode('Halo Ryo Digital Website, saya ingin berkonsultasi tentang '.$service['title'].' untuk bisnis di '.$city['name'].'.') }}" target="_blank" rel="noopener noreferrer">Konsultasi tentang layanan ini <x-arrow-icon/></a>
</section>

<section class="border-y border-white/10 bg-[#171D25] py-16 md:py-20">
    <div class="wrap">
        <div class="flex flex-wrap items-end justify-between gap-5">
            <div><p class="eyebrow">Jurnal untuk {{ $city['name'] }}</p><h2 class="section-title mt-4">Insight & panduan</h2></div>
            <p class="max-w-md text-base leading-7 text-[#A1A4AD]">Artikel terkait {{ strtolower($service['title']) }} yang disiapkan Ryo Digital Website untuk membantu bisnis mengambil keputusan digital dengan lebih terarah.</p>
        </div>
        <div class="mt-9 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @forelse($articles as $article)
                <a href="{{ route('blog.show', $article->slug) }}" class="group border border-white/10 bg-[#11151B] p-6 transition hover:border-emerald-400/40">
                    <p class="eyebrow">{{ $article->category?->name ?? $service['title'] }}</p>
                    <h3 class="mt-4 font-display text-2xl uppercase leading-tight group-hover:text-emerald-400">{{ $article->title }}</h3>
                    <p class="mt-3 text-sm leading-6 text-[#A1A4AD]">{{ $article->excerpt }}</p>
                    <span class="mt-6 inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-emerald-400">Baca artikel <x-arrow-icon/></span>
                </a>
            @empty
                <div class="border-t border-white/15 py-6 text-[#A1A4AD] md:col-span-2 lg:col-span-3">Panduan untuk wilayah ini sedang disiapkan. Hubungi kami untuk mendiskusikan kebutuhan {{ strtolower($service['title']) }} di {{ $city['name'] }}.</div>
            @endforelse
        </div>
        <div class="mt-8">{{ $articles->links() }}</div>
    </div>
</section>
<section class="wrap py-16 md:py-20">@include('components.contact-panel')</section>
@endsection

@push('structured-data')
<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'Service', 'name' => $service['title'].' di '.$city['name'], 'serviceType' => $service['title'], 'areaServed' => ['@type' => 'City', 'name' => $city['name'], 'containedInPlace' => $city['province']], 'provider' => ['@type' => 'Organization', 'name' => 'Ryo Digital Website', 'url' => config('app.url')]], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
@endpush
