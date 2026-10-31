@extends('layouts.app')

@section('content')
<section class="wrap section-space pt-36">
    <nav class="mb-8 text-sm text-[#A1A4AD]" aria-label="Breadcrumb"><a href="{{ route('home') }}" class="hover:text-emerald-400">Beranda</a><span class="px-2 text-emerald-400">/</span><a href="{{ route('regions.index') }}" class="hover:text-emerald-400">Wilayah</a><span class="px-2 text-emerald-400">/</span><a href="{{ route('regions.province',$city['province_slug']) }}" class="hover:text-emerald-400">{{ $city['province'] }}</a><span class="px-2 text-emerald-400">/</span><a href="{{ route('regions.city',$city['slug']) }}" class="hover:text-emerald-400">{{ $city['name'] }}</a><span class="px-2 text-emerald-400">/</span>Kecamatan {{ $district['name'] }}</nav>
    <p class="eyebrow">{{ $city['province'] }} · {{ $city['name'] }}</p><h1 class="display mt-5 max-w-5xl text-[clamp(46px,7.5vw,96px)]">WEBSITE UNTUK BISNIS<br><span class="text-emerald-400">KECAMATAN {{ strtoupper($district['name']) }}.</span></h1>
    <p class="mt-7 max-w-3xl text-lg leading-8 text-[#A1A4AD]">Ryo Digital Website membantu UMKM, penyedia jasa, lembaga pendidikan, dan organisasi di Kecamatan {{ $district['name'] }}, {{ $city['name'] }} membangun kehadiran digital yang jelas dan mudah dikelola.</p>
    <a class="btn btn-primary mt-8" href="https://wa.me/6285648983077?text={{ rawurlencode('Halo Ryo Digital Website, saya ingin konsultasi website dan layanan digital untuk bisnis di Kecamatan '.$district['name'].', '.$city['name'].'.') }}" target="_blank" rel="noopener noreferrer">Konsultasi wilayah ini · 085648983077 <x-arrow-icon/></a>
</section>

<section class="border-y border-white/10 bg-[#171D25] py-16 md:py-20"><div class="wrap grid gap-10 lg:grid-cols-[.8fr_1.2fr]">
    <div><p class="eyebrow">Kenapa bisnis lokal perlu website?</p><h2 class="section-title mt-4">INFORMASI YANG<br><span class="text-emerald-400">MUDAH DIPERCAYA.</span></h2></div>
    <div class="space-y-6 text-base leading-7 text-[#A1A4AD]"><p>Calon pelanggan sering ingin mengetahui apa yang ditawarkan, siapa yang menjalankannya, dan bagaimana cara memulai sebelum mereka menghubungi sebuah usaha. Website memberi tempat yang rapi untuk menjawab pertanyaan tersebut kapan saja.</p><p>Bagi UMKM di Kecamatan {{ $district['name'] }}, website juga dapat menjadi katalog layanan, portofolio, halaman pemesanan, atau pusat informasi. Bentuk terbaiknya bergantung pada cara bisnis beroperasi dan kebutuhan pelanggan—bukan sekadar banyaknya halaman atau fitur.</p><p>Website melengkapi kanal sosial dengan alamat digital yang dapat diatur sendiri. Informasi yang jelas membantu pelanggan mengambil langkah berikutnya dan membantu pemilik usaha menjaga cerita bisnis tetap konsisten.</p></div>
</div></section>

<section class="wrap py-16 md:py-20"><p class="eyebrow">Solusi yang dapat disesuaikan</p><h2 class="section-title mt-4">LAYANAN UNTUK BISNIS<br>DI {{ strtoupper($district['name']) }}.</h2><p class="mb-8 mt-4 max-w-2xl text-base leading-7 text-[#A1A4AD]">Dari website company profile hingga aplikasi web khusus, diskusikan kebutuhan yang paling relevan dengan kegiatan usahamu.</p><x-regional-service-grid :services="$services" :region-name="'Kecamatan '.$district['name'].', '.$city['name']"/></section>
<x-regional-articles :articles="$regionalArticles" :region-name="'Kecamatan '.$district['name']"/>
<section class="border-t border-white/10 bg-[#171D25] py-14"><div class="wrap flex flex-wrap items-center justify-between gap-5"><div><p class="eyebrow">Wilayah administrasi</p><p class="mt-3 text-lg">Kecamatan {{ $district['name'] }} <span class="text-emerald-400">·</span> {{ $city['name'] }}, {{ $city['province'] }}</p></div><a href="{{ route('regions.city',$city['slug']) }}" class="nav-link">Jelajahi kecamatan lain di {{ $city['name'] }} <x-arrow-icon/></a></div></section>
<section class="wrap py-16 md:py-20">@include('components.contact-panel')</section>
@endsection
