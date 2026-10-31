@extends('layouts.app')

@section('content')
<div class="services-page">
    <section class="wrap services-hero">
        <div class="services-hero-grid">
            <div>
                <p class="eyebrow"><span class="eyebrow-mark"></span> Layanan Ryo Digital Website</p>
                <h1 class="display services-title">SOLUSI DIGITAL<br><span>YANG PUNYA</span><br>ARAH JELAS.</h1>
            </div>
            <div class="services-hero-aside">
                <p>Kami membantu bisnis memilih dan membangun solusi digital yang sesuai dengan kebutuhan hari ini dan rencana berikutnya.</p>
                <a class="btn btn-primary" href="{{ route('contact') }}">Diskusikan kebutuhanmu <x-arrow-icon/></a>
                <span class="services-hero-meta">Strategi <i>·</i> Desain <i>·</i> Teknologi</span>
            </div>
        </div>
    </section>

    <section class="services-list-section">
        <div class="wrap">
            <div class="services-section-heading">
                <p class="eyebrow">01 / Apa yang kami kerjakan</p>
                <h2 class="section-title">DARI IDE PERTAMA<br>SAMPAI SIAP DIGUNAKAN.</h2>
            </div>
            <div class="services-grid">
                @foreach(config('ryo.services') as $slug => $service)
                    @php($whatsappMessage = 'Halo Ryo Digital Website, saya ingin konsultasi tentang layanan '.$service['title'].'. Bisa dibantu?')
                    <article class="service-card group">
                        <div class="service-card-top">
                            <span class="service-card-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="service-card-icon"><x-line-icon :name="$service['icon']"/></span>
                        </div>
                        <h3><a href="{{ route('services.show', $slug) }}">{{ $service['title'] }}</a></h3>
                        <p>{{ $service['description'] }}</p>
                        <a class="service-card-link" href="https://wa.me/6285648983077?text={{ rawurlencode($whatsappMessage) }}" target="_blank" rel="noopener noreferrer" aria-label="Konsultasi tentang {{ $service['title'] }} melalui WhatsApp">Konsultasi sekarang <x-arrow-icon/></a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="wrap py-16 md:py-24" aria-labelledby="regional-service-heading">
        <div class="grid gap-10 border border-white/10 bg-[#171D25] p-6 sm:p-9 lg:grid-cols-[.85fr_1.15fr] lg:items-end lg:p-12">
            <div>
                <p class="eyebrow">Jangkauan regional</p>
                <h2 id="regional-service-heading" class="section-title mt-4">DARI MALANG<br>UNTUK INDONESIA.</h2>
                <p class="mt-5 max-w-lg text-base leading-7 text-[#A1A4AD]">Kami bekerja bersama bisnis di berbagai daerah. Pilih wilayah dan layanan untuk memulai konsultasi yang lebih relevan dengan kebutuhanmu.</p>
            </div>
            <form class="grid gap-4 sm:grid-cols-2" data-regional-service-form data-cities-url="{{ route('regional.cities') }}">
                <label class="text-xs font-bold uppercase tracking-wider text-[#A1A4AD]">Layanan<select class="field mt-2" name="service" required><option value="">Pilih layanan</option>@foreach(config('ryo.services') as $slug=>$service)<option value="{{ $slug }}">{{ $service['title'] }}</option>@endforeach</select></label>
                <label class="text-xs font-bold uppercase tracking-wider text-[#A1A4AD]">Provinsi<select class="field mt-2" name="province" data-regional-province><option value="">Semua provinsi</option>@foreach($provinces as $province)<option value="{{ $province['slug'] }}">{{ $province['name'] }}</option>@endforeach</select></label>
                <label class="text-xs font-bold uppercase tracking-wider text-[#A1A4AD]">Cari kota / kabupaten<input class="field mt-2" type="search" name="q" data-regional-city-search placeholder="Contoh: Malang"></label>
                <label class="text-xs font-bold uppercase tracking-wider text-[#A1A4AD]">Pilih wilayah<select class="field mt-2" name="city" data-regional-city required><option value="">Pilih kota atau kabupaten</option></select></label>
                <button class="btn btn-primary sm:col-span-2" type="submit">Konsultasi sesuai wilayah <x-arrow-icon/></button>
                <p class="text-xs leading-5 text-[#A1A4AD] sm:col-span-2">Pilihan wilayah menggunakan master administrasi BPS. Tombol akan membuka WhatsApp dengan layanan dan wilayah pilihanmu.</p>
            </form>
        </div>
    </section>

    <section class="wrap services-concepts">
        <div class="services-concepts-heading">
            <div>
                <p class="eyebrow">02 / Website terpilih</p>
                <h2 class="section-title">KARYA DIGITAL<br><span class="text-emerald-400">KAMI.</span></h2>
            </div>
            <p>Jelajahi beberapa website pilihan untuk layanan profesional dan pelatihan, lalu lihat langsung bagaimana pengalaman digitalnya disusun.</p>
        </div>
        <div class="services-concepts-grid">
            @foreach(config('ryo.projects') as $slug => $project)
                <x-project-card :title="$project['title']" :category="$project['category']" :description="$project['description']" :art="$project['art']" :slug="$slug"/>
            @endforeach
        </div>
        <p class="services-concepts-note"><span></span> Website terpilih · Preview tersedia di halaman portfolio</p>
    </section>

    <section class="wrap services-contact">
        @include('components.contact-panel')
    </section>
</div>
@endsection
