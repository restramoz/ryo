@extends('layouts.app')

@section('content')
<section class="wrap section-space pt-36">
    <nav class="mb-8 text-sm text-[#A1A4AD]" aria-label="Breadcrumb"><a href="{{ route('home') }}" class="hover:text-emerald-400">Beranda</a><span class="px-2 text-emerald-400">/</span>Wilayah</nav>
    <div class="grid gap-8 lg:grid-cols-[1.1fr_.9fr] lg:items-end">
        <div><p class="eyebrow">Jangkauan Ryo Digital Website</p><h1 class="display mt-5 text-[clamp(52px,9vw,112px)]">DIGITAL YANG<br><span class="text-emerald-400">LEBIH DEKAT.</span></h1></div>
        <p class="max-w-xl text-lg leading-8 text-[#A1A4AD]">Jelajahi informasi layanan website dan digital berdasarkan wilayah administrasi Indonesia. Data provinsi, kabupaten/kota, serta kecamatan disusun dari master wilayah BPS.</p>
    </div>
    <div class="mt-12 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($provinces as $province)
            <a href="{{ route('regions.province', $province['slug']) }}" class="group border-t border-white/15 py-5 transition hover:border-emerald-400/50">
                <div class="flex items-start justify-between gap-5"><div><h2 class="font-display text-2xl uppercase group-hover:text-emerald-300">{{ $province['name'] }}</h2><p class="mt-2 text-xs tracking-wide text-[#A1A4AD]">{{ $province['city_count'] }} kabupaten/kota <span class="px-1 text-emerald-400">·</span> {{ $province['district_count'] }} kecamatan</p></div><span class="text-emerald-400"><x-arrow-icon/></span></div>
            </a>
        @endforeach
    </div>
</section>
<section class="border-y border-white/10 bg-[#171D25] py-16 md:py-20"><div class="wrap"><p class="eyebrow">Website untuk bisnis lokal</p><h2 class="section-title mt-4">PUNYA RUANG DIGITAL<br>YANG BISA DIANDALKAN.</h2><p class="mt-6 max-w-3xl text-base leading-7 text-[#A1A4AD]">Website membantu UMKM dan bisnis menjelaskan produk, layanan, cara kerja, serta informasi kontak dalam satu tempat yang mudah dibagikan. Website juga memberi kendali lebih besar atas pengalaman pelanggan dibanding hanya mengandalkan profil media sosial. Pilihan solusi tetap perlu disesuaikan dengan tujuan, audiens, dan kapasitas operasional masing-masing bisnis.</p></div></section>
@endsection
