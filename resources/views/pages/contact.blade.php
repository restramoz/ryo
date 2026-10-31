@extends('layouts.app')

@section('content')
<div class="contact-page">
    <section class="wrap contact-hero">
        <div class="contact-intro">
            <p class="eyebrow"><span class="eyebrow-mark"></span> Mari mulai percakapan</p>
            <h1 class="display contact-title">PUNYA IDE?<br><span>MARI BICARAKAN.</span></h1>
            <p class="contact-lead">Setiap website yang tepat dimulai dari percakapan yang jujur tentang kebutuhan bisnis. Ceritakan apa yang ingin kamu capai—kita cari langkah yang relevan bersama.</p>

            <a class="contact-direct-link" href="https://wa.me/6285648983077?text={{ rawurlencode('Halo Ryo Digital Website, saya ingin berdiskusi tentang proyek website.') }}" target="_blank" rel="noopener noreferrer">
                <span class="contact-direct-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 11.5a7.5 7.5 0 0 1-11.2 6.5L4 19l1-4.5A7.5 7.5 0 1 1 20 11.5Z"/><path d="M9 9.2c.5 2 1.8 3.3 3.8 3.8"/></svg>
                </span>
                <span><small>Ingin ngobrol langsung?</small><strong>Hubungi via WhatsApp</strong></span>
                <x-arrow-icon/>
            </a>

            <div class="contact-details">
                <p class="eyebrow">Informasi kontak</p>
                <a class="contact-detail-row" href="https://wa.me/6285648983077" target="_blank" rel="noopener noreferrer">
                    <span class="contact-detail-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 11.5a7.5 7.5 0 0 1-11.2 6.5L4 19l1-4.5A7.5 7.5 0 1 1 20 11.5Z"/></svg></span>
                    <span><small>WhatsApp</small><strong>0856 4898 3077</strong></span>
                    <x-arrow-icon/>
                </a>
                @if($siteSettings['email'] ?? config('ryo.email'))
                    <a class="contact-detail-row" href="mailto:{{ $siteSettings['email'] ?? config('ryo.email') }}">
                        <span class="contact-detail-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="5" width="18" height="14" rx="1.5"/><path d="m4 7 8 6 8-6"/></svg></span>
                        <span><small>Email</small><strong>{{ $siteSettings['email'] ?? config('ryo.email') }}</strong></span>
                        <x-arrow-icon/>
                    </a>
                @endif
                @if($siteSettings['location'] ?? config('ryo.location'))
                    <div class="contact-detail-row">
                        <span class="contact-detail-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.2"/></svg></span>
                        <span><small>Lokasi</small><strong>{{ $siteSettings['location'] ?? config('ryo.location') }}</strong></span>
                    </div>
                @endif
                @php($contactSocials = collect([
                    'Instagram' => $siteSettings['instagram'] ?? config('ryo.social.Instagram'),
                    'LinkedIn' => $siteSettings['linkedin'] ?? config('ryo.social.LinkedIn'),
                    'Behance' => $siteSettings['behance'] ?? config('ryo.social.Behance'),
                ])->filter())
                @if($contactSocials->isNotEmpty())
                    <div class="contact-socials">
                        <span class="eyebrow">Temukan kami</span>
                        @foreach($contactSocials as $network => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer">{{ $network }} <x-arrow-icon/></a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="contact-form-wrap">
            <div class="contact-form-heading">
                <p class="eyebrow">Mulai dari sini</p>
                <h2>Ceritakan proyekmu.</h2>
                <p>Isi ringkasan singkat. Pesan akan disiapkan dan dibuka di WhatsApp.</p>
            </div>
            <form class="contact-form" data-whatsapp-contact-form>
                <div class="contact-form-row">
                    <label for="contact-name">Nama <span>*</span>
                        <input class="field" id="contact-name" name="name" type="text" autocomplete="name" placeholder="Nama kamu" required>
                    </label>
                    <label for="contact-business">Bisnis / organisasi
                        <input class="field" id="contact-business" name="business" type="text" autocomplete="organization" placeholder="Nama bisnis (opsional)">
                    </label>
                </div>
                <label for="contact-channel">Nomor WhatsApp atau email <span>*</span>
                    <input class="field" id="contact-channel" name="contact" type="text" autocomplete="email" placeholder="Cara terbaik menghubungimu" required>
                </label>
                <label for="contact-service">Layanan yang dibutuhkan <span>*</span>
                    <select class="field" id="contact-service" name="service" required>
                        <option value="" disabled selected>Pilih layanan</option>
                        @foreach(config('ryo.services') as $service)
                            <option value="{{ $service['title'] }}">{{ $service['title'] }}</option>
                        @endforeach
                        <option value="Belum yakin / ingin konsultasi">Belum yakin / ingin konsultasi</option>
                    </select>
                </label>
                <label for="contact-message">Ceritakan kebutuhanmu <span>*</span>
                    <textarea class="field" id="contact-message" name="message" rows="4" placeholder="Apa yang ingin kamu bangun atau perbaiki?" required></textarea>
                </label>
                <button class="btn btn-primary contact-submit" type="submit">Lanjut ke WhatsApp <x-arrow-icon/></button>
                <p class="contact-form-note"><span aria-hidden="true">i</span> Pesan tidak disimpan di website ini. Kamu dapat meninjau dan mengirimkannya melalui WhatsApp.</p>
            </form>
        </div>
    </section>
</div>
@endsection
