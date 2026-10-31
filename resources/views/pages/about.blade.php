@extends('layouts.app')

@section('content')
<div class="about-page">
    <section class="wrap about-hero">
        <div class="about-hero-grid">
            <div class="about-hero-copy">
                <p class="eyebrow"><span class="eyebrow-mark"></span> Tentang Ryo Digital Website</p>
                <h1 class="display about-title">BISNIS YANG<br><span>BERGERAK.</span><br>DENGAN ARAH<br>DIGITAL YANG JELAS.</h1>
            </div>
            <div class="about-hero-rail">
                <div class="about-identity-mark" aria-hidden="true"><span>RD</span></div>
                <p class="eyebrow">Strategi · Desain · Teknologi</p>
                <p class="about-hero-intro">Ryo Digital Website adalah partner digital untuk bisnis yang ingin hadir lebih jelas, terasa meyakinkan, dan siap melangkah ke tahap berikutnya.</p>
                <p class="about-hero-note">Kami merangkai kebutuhan bisnis dan pengalaman pengguna menjadi website yang punya tujuan.</p>
            </div>
        </div>
    </section>

    <section class="about-direction">
        <div class="wrap">
            <div class="about-section-heading">
                <p class="eyebrow">Arah yang kami tuju</p>
                <h2 class="section-title">DIGITAL YANG<br><span class="about-highlight">BERARTI.</span></h2>
            </div>
            <div class="about-direction-grid">
                <article class="about-direction-card">
                    <span class="about-index">01 / VISI</span>
                    <h3>Menjadi partner digital yang membantu bisnis Indonesia bertumbuh dengan percaya diri.</h3>
                    <p>Kami ingin membantu lebih banyak bisnis membangun kehadiran digital yang jelas, berguna, dan dekat dengan kebutuhan orang yang mereka layani.</p>
                </article>
                <article class="about-direction-card about-mission">
                    <span class="about-index">02 / MISI</span>
                    <h3>Mengubah tujuan bisnis menjadi pengalaman digital yang bisa digunakan dan terus dikembangkan.</h3>
                    <ul>
                        <li><span>01</span> Memahami kebutuhan bisnis dan pengguna sebelum menentukan solusi.</li>
                        <li><span>02</span> Merancang dan membangun website dengan perhatian pada kejelasan, kualitas, dan kemudahan penggunaan.</li>
                        <li><span>03</span> Menjadi partner yang terbuka selama proses, dari perencanaan hingga pengembangan berikutnya.</li>
                    </ul>
                </article>
            </div>
        </div>
    </section>

    <section class="wrap about-values">
        <div class="about-section-heading about-values-heading">
            <p class="eyebrow">Prinsip kerja</p>
            <h2 class="section-title">CARA KAMI<br><span class="about-highlight">BEKERJA.</span></h2>
        </div>
        <div class="about-values-grid">
            <article class="about-value">
                <span class="about-index">01</span>
                <h3>Dengar sebelum membangun</h3>
                <p>Setiap proyek dimulai dari memahami tujuan, tantangan, dan orang-orang yang akan menggunakan website.</p>
            </article>
            <article class="about-value">
                <span class="about-index">02</span>
                <h3>Jelas di setiap langkah</h3>
                <p>Ruang lingkup, keputusan desain, dan langkah pengerjaan dibicarakan bersama agar arah proyek mudah diikuti.</p>
            </article>
            <article class="about-value">
                <span class="about-index">03</span>
                <h3>Dibuat untuk berkembang</h3>
                <p>Kami mempertimbangkan kebutuhan hari ini sekaligus ruang untuk perbaikan saat bisnis terus bergerak.</p>
            </article>
        </div>
    </section>

    <section class="about-faq">
        <div class="wrap about-faq-layout">
            <div class="about-faq-intro">
                <p class="eyebrow">Tanya jawab</p>
                <h2 class="section-title">SEBELUM<br><span class="about-highlight">MULAI.</span></h2>
                <p>Beberapa hal yang sering ingin diketahui sebelum memulai proyek bersama Ryo Digital Website.</p>
                <a class="nav-link about-faq-contact" href="{{ route('contact') }}">Masih ada pertanyaan? Hubungi kami <x-arrow-icon/></a>
            </div>

            <div id="about-faq-list" class="about-faq-list" data-accordion="collapse">
                <article class="about-faq-item">
                    <h3 id="about-faq-heading-1">
                        <button type="button" aria-expanded="true" aria-controls="about-faq-answer-1" data-accordion-target="#about-faq-answer-1">
                            <span>Apa itu Ryo Digital Website?</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
                        </button>
                    </h3>
                    <div id="about-faq-answer-1" class="about-faq-answer" aria-labelledby="about-faq-heading-1">Ryo Digital Website adalah studio digital yang membantu bisnis merencanakan, merancang, dan membangun website serta solusi digital sesuai kebutuhan mereka.</div>
                </article>
                <article class="about-faq-item">
                    <h3 id="about-faq-heading-2">
                        <button type="button" aria-expanded="false" aria-controls="about-faq-answer-2" data-accordion-target="#about-faq-answer-2">
                            <span>Bisnis seperti apa yang bisa bekerja sama dengan Ryo Digital Website?</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
                        </button>
                    </h3>
                    <div id="about-faq-answer-2" class="about-faq-answer hidden" aria-labelledby="about-faq-heading-2">Kami terbuka untuk UMKM, perusahaan, organisasi, lembaga pendidikan, penyedia jasa profesional, dan personal brand. Solusi dan ruang lingkupnya disesuaikan dengan kebutuhan masing-masing.</div>
                </article>
                <article class="about-faq-item">
                    <h3 id="about-faq-heading-3">
                        <button type="button" aria-expanded="false" aria-controls="about-faq-answer-3" data-accordion-target="#about-faq-answer-3">
                            <span>Layanan apa saja yang tersedia?</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
                        </button>
                    </h3>
                    <div id="about-faq-answer-3" class="about-faq-answer hidden" aria-labelledby="about-faq-heading-3">Layanan kami mencakup website company profile, landing page, e-commerce, website pendidikan, aplikasi web khusus, redesign, SEO dan perawatan website, serta otomasi AI.</div>
                </article>
                <article class="about-faq-item">
                    <h3 id="about-faq-heading-4">
                        <button type="button" aria-expanded="false" aria-controls="about-faq-answer-4" data-accordion-target="#about-faq-answer-4">
                            <span>Bagaimana proses memulai proyek?</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
                        </button>
                    </h3>
                    <div id="about-faq-answer-4" class="about-faq-answer hidden" aria-labelledby="about-faq-heading-4">Mulai dengan menceritakan tujuan dan kebutuhanmu. Kami akan berdiskusi, menyusun ruang lingkup yang sesuai, lalu menyepakati langkah kerja sebelum proses desain dan pengembangan dimulai.</div>
                </article>
                <article class="about-faq-item">
                    <h3 id="about-faq-heading-5">
                        <button type="button" aria-expanded="false" aria-controls="about-faq-answer-5" data-accordion-target="#about-faq-answer-5">
                            <span>Apakah Ryo Digital Website bisa membantu website yang sudah ada?</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
                        </button>
                    </h3>
                    <div id="about-faq-answer-5" class="about-faq-answer hidden" aria-labelledby="about-faq-heading-5">Bisa dibicarakan. Kami akan meninjau kondisi website dan kebutuhanmu terlebih dahulu untuk menentukan bentuk perbaikan atau pengembangan yang paling sesuai.</div>
                </article>
                <article class="about-faq-item">
                    <h3 id="about-faq-heading-6">
                        <button type="button" aria-expanded="false" aria-controls="about-faq-answer-6" data-accordion-target="#about-faq-answer-6">
                            <span>Apakah ada dukungan setelah website diluncurkan?</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
                        </button>
                    </h3>
                    <div id="about-faq-answer-6" class="about-faq-answer hidden" aria-labelledby="about-faq-heading-6">Dukungan lanjutan dapat dibahas sesuai kebutuhan proyek, seperti pemeliharaan, pembaruan konten, atau pengembangan fitur berikutnya.</div>
                </article>
            </div>
        </div>
    </section>

    <section class="wrap about-contact">
        @include('components.contact-panel')
    </section>
</div>
@endsection
