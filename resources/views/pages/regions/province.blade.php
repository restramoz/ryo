@extends('layouts.app')

@section('content')
<section class="wrap section-space pt-36">
    <nav class="mb-8 text-sm text-[#A1A4AD]" aria-label="Breadcrumb"><a href="{{ route('home') }}" class="hover:text-emerald-400">Beranda</a><span class="px-2 text-emerald-400">/</span><a href="{{ route('regions.index') }}" class="hover:text-emerald-400">Wilayah</a><span class="px-2 text-emerald-400">/</span>{{ $province['name'] }}</nav>
    <p class="eyebrow">Provinsi · {{ $province['name'] }}</p>
    <h1 class="display mt-5 text-[clamp(48px,8vw,104px)]">WEBSITE &amp; LAYANAN<br><span class="text-emerald-400">DIGITAL {{ strtoupper($province['name']) }}.</span></h1>
    <p class="mt-7 max-w-3xl text-lg leading-8 text-[#A1A4AD]">Ryo Digital Website membantu UMKM, perusahaan, lembaga pendidikan, penyedia jasa profesional, dan personal brand di {{ $province['name'] }} membangun rumah digital yang menjelaskan nilai dan layanan mereka dengan lebih terarah.</p>
    <div class="mt-7 flex flex-wrap gap-x-7 gap-y-2 text-xs font-bold uppercase tracking-widest text-[#A1A4AD]"><span>{{ count($cities) }} kabupaten/kota</span><span class="text-emerald-400">{{ number_format($districtCount) }} kecamatan</span></div>
</section>

<section class="border-y border-white/10 bg-[#171D25] py-16 md:py-20"><div class="wrap grid gap-10 lg:grid-cols-[.72fr_1.28fr]">
    <div><p class="eyebrow">Mengapa website penting?</p><h2 class="section-title mt-4">BISNIS LOKAL<br>BUTUH RUANG<br><span class="text-emerald-400">YANG DIMILIKI.</span></h2></div>
    <div class="grid gap-6 sm:grid-cols-2">
        @foreach([['Informasi yang selalu tersedia','Pelanggan dapat mempelajari layanan, lokasi, jam operasional, dan cara menghubungi bisnis tanpa menunggu balasan pesan.'],['Membangun kepercayaan','Profil usaha, portofolio, proses, dan informasi yang tersusun rapi membantu calon pelanggan memahami siapa yang mereka hubungi.'],['Lebih mudah ditemukan dan dibagikan','Halaman khusus untuk layanan dan pertanyaan pelanggan memberi tempat yang jelas untuk dibagikan lewat pencarian maupun kanal digital lain.'],['Kanal digital milik sendiri','Website melengkapi media sosial dengan ruang yang lebih fleksibel untuk mengatur konten, alur, dan fitur sesuai kebutuhan usaha.']] as $point)
            <article class="border-t border-white/15 pt-4"><h3 class="font-display text-xl uppercase">{{ $point[0] }}</h3><p class="mt-2 text-sm leading-6 text-[#A1A4AD]">{{ $point[1] }}</p></article>
        @endforeach
    </div>
</div></section>

<section class="wrap py-16 md:py-20"><p class="eyebrow">Solusi untuk bisnis di {{ $province['name'] }}</p><h2 class="section-title mt-4">LAYANAN RYO DIGITAL WEBSITE.</h2><p class="mb-8 mt-4 max-w-2xl text-base leading-7 text-[#A1A4AD]">Pilih jenis dukungan yang paling dekat dengan kebutuhan bisnismu. Ruang lingkup dan teknologi akan dibahas bersama sebelum proyek dimulai.</p><x-regional-service-grid :services="$services" :region-name="$province['name']"/></section>

<x-regional-articles :articles="$regionalArticles" :region-name="$province['name']"/>

<section class="border-t border-white/10 bg-[#171D25] py-16"><div class="wrap"><div class="flex flex-wrap items-end justify-between gap-5"><div><p class="eyebrow">Jelajahi wilayah</p><h2 class="section-title mt-3">KABUPATEN &amp; KOTA.</h2></div><a href="{{ route('regions.index') }}" class="nav-link">Semua provinsi <x-arrow-icon/></a></div><div class="mt-7 grid gap-x-6 sm:grid-cols-2 lg:grid-cols-3">@foreach($cities as $city)<a href="{{ route('regions.city',$city['slug']) }}" class="border-t border-white/10 py-4 text-sm text-[#F3F4F2] hover:text-emerald-300">{{ $city['name'] }} <span class="text-[#A1A4AD]">· {{ $districtCounts[$city['slug']] ?? 0 }} kecamatan</span></a>@endforeach</div></div></section>
<section class="wrap py-16 md:py-20">@include('components.contact-panel')</section>
@endsection
