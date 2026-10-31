@extends('layouts.app')

@section('content')
<div class="pricing-page">
    <section class="wrap pricing-hero">
        <div class="pricing-hero-main">
            <p class="eyebrow"><span class="eyebrow-mark"></span> Ruang lingkup & estimasi</p>
            <h1 class="display pricing-title">INVESTASI DIGITAL<br><span>YANG TERARAH.</span></h1>
        </div>
        <div class="pricing-hero-aside">
            <p>Setiap bisnis punya kebutuhan yang berbeda. Pilih jenis solusi yang paling dekat dengan rencanamu—kami bantu petakan lingkup dan estimasinya.</p>
            <span class="pricing-quote-note"><i></i> Penawaran disusun sesuai lingkup proyek</span>
        </div>
    </section>

    <section class="pricing-options-section">
        <div class="wrap">
            <div class="pricing-section-heading">
                <div>
                    <p class="eyebrow">01 / Pilih kebutuhan</p>
                    <h2 class="section-title">MULAI DARI TUJUAN<br>YANG INGIN DICAPAI.</h2>
                </div>
                <p>Gambaran ini membantu menentukan arah awal. Rincian dan biaya final dibahas setelah kebutuhan proyek dipahami.</p>
            </div>

            <div class="pricing-cards">
                @foreach(config('ryo.pricing') as $slug => $package)
                    @php($pricingMessage = 'Halo Ryo Digital Website, saya ingin meminta estimasi untuk '.$package['name'].'.')
                    <article class="pricing-card">
                        <div class="pricing-card-top">
                            <span class="pricing-card-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }} / SOLUSI</span>
                            <span class="pricing-card-icon"><x-line-icon :name="$package['icon']"/></span>
                        </div>
                        <p class="pricing-card-label">Penawaran sesuai lingkup</p>
                        <h3>{{ $package['name'] }}</h3>
                        <p class="pricing-card-fit">{{ $package['fit'] }}</p>
                        <div class="pricing-examples">
                            <span class="eyebrow">Contoh kebutuhan</span>
                            <ul>
                                @foreach($package['examples'] as $example)
                                    <li><span aria-hidden="true"></span>{{ $example }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <a class="btn btn-primary pricing-card-cta" href="https://wa.me/6285648983077?text={{ rawurlencode($pricingMessage) }}" target="_blank" rel="noopener noreferrer">Minta estimasi <x-arrow-icon/></a>
                    </article>
                @endforeach
            </div>

            <div class="pricing-custom-note">
                <span class="pricing-custom-mark"><x-line-icon name="discover"/></span>
                <div><p class="eyebrow">Belum yakin mulai dari mana?</p><p>Ceritakan tujuan dan tantangan bisnismu. Kita bisa membahas kebutuhan yang paling penting terlebih dahulu.</p></div>
                <a href="{{ route('contact') }}">Diskusikan kebutuhan <x-arrow-icon/></a>
            </div>
        </div>
    </section>

    <section class="wrap pricing-factors">
        <div class="pricing-section-heading">
            <div>
                <p class="eyebrow">02 / Dasar perhitungan</p>
                <h2 class="section-title">APA YANG MEMBENTUK<br><span class="text-emerald-400">ESTIMASI?</span></h2>
            </div>
            <p>Estimasi disusun dari lingkup yang disepakati, agar prioritas dan kebutuhan proyek terlihat sejak awal.</p>
        </div>
        <div class="pricing-factor-grid">
            @foreach([
                ['01', 'Ruang lingkup', 'Jumlah halaman, alur, dan bagian yang perlu dibangun.'],
                ['02', 'Fitur & integrasi', 'Kebutuhan fungsional serta layanan lain yang perlu terhubung.'],
                ['03', 'Materi & konten', 'Kesiapan teks, foto, data, atau materi yang akan digunakan.'],
                ['04', 'Rencana pengerjaan', 'Prioritas, tahapan, dan waktu yang dibutuhkan untuk menyelesaikan proyek.'],
            ] as $factor)
                <article class="pricing-factor">
                    <span class="pricing-factor-index">{{ $factor[0] }}</span>
                    <h3>{{ $factor[1] }}</h3>
                    <p>{{ $factor[2] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="pricing-faq-section">
        <div class="wrap pricing-faq-layout">
            <div class="pricing-faq-intro">
                <p class="eyebrow">03 / Pertanyaan umum</p>
                <h2 class="section-title">SEBELUM<br><span class="text-emerald-400">MULAI.</span></h2>
                <p>Hal yang sering ditanyakan saat merencanakan anggaran website dan solusi digital.</p>
            </div>
            <div id="pricing-faq" data-accordion="collapse" class="pricing-faq-list">
                <article class="pricing-faq-item">
                    <h3 id="pricing-faq-heading-one"><button type="button" data-accordion-target="#pricing-faq-body-one" aria-expanded="true" aria-controls="pricing-faq-body-one"><span>Kenapa tidak ada harga tetap di halaman ini?</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M5 12h14M12 5v14"/></svg></button></h3>
                    <div id="pricing-faq-body-one" class="pricing-faq-answer" aria-labelledby="pricing-faq-heading-one">Kebutuhan tiap website dapat berbeda dari sisi lingkup, fitur, dan materi. Estimasi dibahas berdasarkan kebutuhan proyek agar rincian yang ditawarkan relevan.</div>
                </article>
                <article class="pricing-faq-item">
                    <h3 id="pricing-faq-heading-two"><button type="button" data-accordion-target="#pricing-faq-body-two" aria-expanded="false" aria-controls="pricing-faq-body-two"><span>Bisa mulai dari kebutuhan yang sederhana?</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M5 12h14M12 5v14"/></svg></button></h3>
                    <div id="pricing-faq-body-two" class="hidden pricing-faq-answer" aria-labelledby="pricing-faq-heading-two">Bisa. Kita dapat membahas prioritas utama terlebih dahulu, lalu menyusun lingkup yang sesuai dengan tujuan dan kesiapan bisnismu.</div>
                </article>
                <article class="pricing-faq-item">
                    <h3 id="pricing-faq-heading-three"><button type="button" data-accordion-target="#pricing-faq-body-three" aria-expanded="false" aria-controls="pricing-faq-body-three"><span>Apa yang perlu disiapkan untuk meminta estimasi?</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M5 12h14M12 5v14"/></svg></button></h3>
                    <div id="pricing-faq-body-three" class="hidden pricing-faq-answer" aria-labelledby="pricing-faq-heading-three">Siapkan gambaran tujuan website, siapa yang akan menggunakannya, fitur yang dibutuhkan, dan referensi jika ada. Detailnya bisa dibicarakan bersama.</div>
                </article>
                <article class="pricing-faq-item">
                    <h3 id="pricing-faq-heading-four"><button type="button" data-accordion-target="#pricing-faq-body-four" aria-expanded="false" aria-controls="pricing-faq-body-four"><span>Apakah perawatan website termasuk dalam biaya?</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M5 12h14M12 5v14"/></svg></button></h3>
                    <div id="pricing-faq-body-four" class="hidden pricing-faq-answer" aria-labelledby="pricing-faq-heading-four">SEO, perawatan, dan dukungan dapat dibahas sebagai kebutuhan tersendiri. Cakupan layanan dan biayanya dicantumkan dalam penawaran sesuai kebutuhan.</div>
                </article>
            </div>
        </div>
    </section>

    <section class="wrap pricing-contact">
        @include('components.contact-panel')
    </section>
</div>
@endsection
