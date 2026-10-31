@extends('layouts.app')

@section('content')
<div class="portfolio-page">
    <section class="wrap portfolio-hero">
        <div class="portfolio-hero-main">
            <p class="eyebrow"><span class="eyebrow-mark"></span> Portfolio Ryo Digital Website</p>
            <h1 class="display portfolio-title">WEBSITE YANG<br><span>PUNYA ARAH.</span></h1>
        </div>
        <div class="portfolio-hero-aside">
            <p>Jelajahi website yang kami bangun untuk kebutuhan layanan profesional dan pelatihan. Pilih proyek untuk melihat preview, lalu kunjungi situsnya secara langsung.</p>
            <span class="portfolio-count"><b>{{ str_pad((string) count($projects), 2, '0', STR_PAD_LEFT) }}</b> WEBSITE TERPILIH</span>
        </div>
    </section>

    <section class="portfolio-showcase-section">
        <div class="wrap">
            <div class="portfolio-section-heading">
                <div>
                    <p class="eyebrow">01 / Jelajahi proyek</p>
                    <h2 class="section-title">PILIH WEBSITE<br>UNTUK DILIHAT.</h2>
                </div>
                <p>Gunakan tab untuk berpindah antar proyek. Setiap preview ditampilkan di dalam browser frame agar kamu bisa melihat konteks halaman dengan nyaman.</p>
            </div>

            <div class="portfolio-tabs-wrap">
                <ul class="portfolio-tabs" id="portfolio-tabs" data-tabs-toggle="#portfolio-tab-content" data-tabs-active-classes="portfolio-tab-active" data-tabs-inactive-classes="portfolio-tab-inactive" role="tablist">
                    @foreach($projects as $slug => $project)
                        <li role="presentation">
                            <button id="{{ $slug }}-tab" class="portfolio-tab {{ $loop->first ? 'portfolio-tab-active' : 'portfolio-tab-inactive' }}" data-tabs-target="#{{ $slug }}-panel" type="button" role="tab" aria-controls="{{ $slug }}-panel" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                <span class="portfolio-tab-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span>{{ $project['title'] }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div id="portfolio-tab-content" class="portfolio-tab-content">
                @foreach($projects as $slug => $project)
                    <div id="{{ $slug }}-panel" class="{{ $loop->first ? '' : 'hidden' }}" role="tabpanel" aria-labelledby="{{ $slug }}-tab" tabindex="0">
                        <x-portfolio-preview :project="$project"/>
                    </div>
                @endforeach
            </div>

            <div class="portfolio-bottom-note">
                <span></span>
                <p>Beberapa website menonaktifkan embed dari domain lain. Jika preview terbatas, gunakan tombol kunjungi website untuk membuka situs penuh.</p>
            </div>
        </div>
    </section>

    <section class="wrap portfolio-contact">
        @include('components.contact-panel')
    </section>
</div>
@endsection
