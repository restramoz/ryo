@extends('layouts.app')

@section('content')
<div class="home-page">
    <section class="wrap hero home-hero">
        <div class="hero-copy-wrap relative z-10">
            <p class="eyebrow hero-eyebrow"><span class="eyebrow-mark"></span> Studio digital untuk bisnis Indonesia</p>
            <h1 class="display hero-title">BISNISMU<br><span class="hero-accent">PUNYA</span><br><span class="hero-accent">CERITA.</span><br>KAMI<br>BANGUN<br>PANGGUNG<br>NYA.</h1>
            <p class="hero-copy mt-7">Ryo Digital Website membantu UMKM, perusahaan, lembaga pendidikan, dan personal brand membangun website yang memperjelas nilai mereka dan menumbuhkan kepercayaan.</p>
            <div class="hero-actions mt-7 flex flex-wrap gap-3">
                <a class="btn btn-primary" href="{{ route('portfolio') }}">Lihat arah karya <x-arrow-icon/></a>
                <a class="btn btn-quiet" href="{{ route('contact') }}">Ceritakan idemu</a>
            </div>
            <div class="hero-proof"><span class="hero-proof-line"></span><span>Strategi <i>·</i> Desain <i>·</i> Teknologi</span></div>
        </div>

        <div class="hero-visual" role="group" aria-label="Kolase editorial tentang proses membangun pengalaman digital">
            <div class="hero-orbit hero-orbit-one"></div>
            <div class="hero-orbit hero-orbit-two"></div>
            <div class="hero-artboard">
                <div class="artboard-top"><span class="artboard-brand"><b>RD</b> / RYO DIGITAL WEBSITE</span><span>CATATAN STUDIO <span class="text-emerald-700">— 01</span></span></div>
                <div class="artboard-center">
                    <span class="artboard-index">ARAH DIGITAL<br>RYO DIGITAL WEBSITE</span>
                    <svg class="artboard-mark" viewBox="0 0 360 330" fill="none" role="img" aria-label="Bentuk abstrak berwarna hijau zamrud">
                        <defs>
                            <linearGradient id="markFill" x1="46" y1="37" x2="317" y2="302" gradientUnits="userSpaceOnUse"><stop stop-color="#00D69A"/><stop offset=".48" stop-color="#10B981"/><stop offset="1" stop-color="#065F46"/></linearGradient>
                            <linearGradient id="markCut" x1="111" y1="77" x2="264" y2="257" gradientUnits="userSpaceOnUse"><stop stop-color="#131B1C"/><stop offset="1" stop-color="#263431"/></linearGradient>
                        </defs>
                        <path d="M179 17c29 0 55 13 71 35 13 18 17 40 12 62 28-4 55 5 72 25 18 21 23 50 13 76-10 27-33 44-61 48-2 26-17 49-40 62-24 14-53 13-76-1-19-12-32-31-35-53-24 10-52 8-73-6-23-15-36-41-33-68 3-28 21-51 47-62-15-21-19-49-9-73 10-26 33-44 61-47 15-1 29 2 42 9 9-5 19-7 29-7Z" fill="url(#markFill)"/>
                        <path d="M181 72c16 0 30 7 38 20 7 10 9 23 6 35 16-2 31 3 41 14 11 12 14 28 8 43-5 15-19 25-35 27-1 15-10 28-23 36-14 8-30 8-43 0-11-7-18-18-20-30-14 6-30 5-42-3-13-9-20-24-19-39 2-16 12-29 27-35-9-12-11-28-5-42 6-15 19-25 35-27 9-1 17 1 24 5 5-3 11-4 17-4Z" fill="url(#markCut)"/>
                        <circle cx="181" cy="164" r="20" fill="#F0EFE5"/>
                        <path d="M286 54v44m-22-22h44M69 234v30m-15-15h30" stroke="#15352D" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="299" cy="244" r="7" fill="#10B981"/>
                    </svg>
                    <span class="artboard-caption">BANGUN DIGITAL<br>DENGAN TUJUAN.</span>
                </div>
                <div class="artboard-bottom"><span>RYO DIGITAL WEBSITE®</span><span>IDENTITAS · PENGALAMAN · PERTUMBUHAN</span><span>01—03</span></div>
            </div>
            <div class="availability"><span class="pulse"></span><span>TERBUKA UNTUK<br>PROYEK BARU</span></div>
            <span class="art-vertical">DIGITAL DENGAN ARAH</span>
        </div>
    </section>

    <section class="wrap home-intro reveal">
        <p class="eyebrow">Bukan sekadar tampil online</p>
        <p class="home-intro-copy">Kami membantu bisnis mengubah <span>ide menjadi pengalaman digital</span> yang jelas, berkarakter, dan siap membawa mereka ke langkah berikutnya.</p>
    </section>

    <section class="wrap home-metrics">
        <div class="stat-panel reveal">
            <div class="stat"><span class="stat-index">01 / STRATEGI</span><strong>Mulai dari tujuan</strong><span>Kebutuhan bisnis jadi arah kerja.</span></div>
            <div class="stat"><span class="stat-index">02 / DESAIN</span><strong>Jelas dan terasa</strong><span>Identitas yang mudah diingat.</span></div>
            <div class="stat"><span class="stat-index">03 / TEKNOLOGI</span><strong>Dibangun cermat</strong><span>Fondasi yang siap berkembang.</span></div>
            <div class="stat"><span class="stat-index">04 / KEMITRAAN</span><strong>Terus bertumbuh</strong><span>Partner sejak ide hingga rilis.</span></div>
        </div>
    </section>

    <section class="section-space home-work">
        <div class="wrap">
            <x-section-heading eyebrow="Website yang telah dibangun" title="Arah digital, dengan karakter." link="Lihat semua arah" :href="route('portfolio')"/>
            <div class="grid gap-5 md:grid-cols-3">
                @foreach(config('ryo.projects') as $slug => $project)
                    <x-project-card :title="$project['title']" :category="$project['category']" :description="$project['description']" :art="$project['art']" :slug="$slug"/>
                @endforeach
            </div>
            <p class="home-note"><span>PORTFOLIO</span> Lihat website pilihan kami untuk layanan profesional dan pelatihan. Ceritakan bisnis dan tantanganmu—kami susun arah yang sesuai.</p>
        </div>
    </section>

    <section class="section-space home-capabilities">
        <div class="wrap">
            <div class="home-capabilities-head">
                <div><p class="eyebrow">Dari fondasi sampai detail</p><h2 class="section-title">SATU PARTNER.<br><span class="text-emerald-400">ARAH YANG UTUH.</span></h2></div>
                <p>Kami merangkai strategi, desain, dan teknologi agar setiap bagian website punya tujuan—bukan hanya terlihat bagus.</p>
            </div>
            <div class="home-services-grid">
                @foreach([
                    ['website-development', 'Website untuk bisnis', 'Company profile, website organisasi, hingga situs personal yang membangun kepercayaan.', 'website'],
                    ['landing-pages', 'Landing page', 'Halaman fokus untuk kampanye, layanan, atau penawaran yang perlu dipahami dengan cepat.', 'landing'],
                    ['e-commerce', 'Toko online', 'Pengalaman belanja yang rapi, mudah dijelajahi, dan nyaman digunakan.', 'commerce'],
                    ['web-applications', 'Aplikasi web khusus', 'Solusi digital yang menyesuaikan proses kerja dan kebutuhan timmu.', 'application'],
                    ['ui-ux-design', 'UI/UX & redesign', 'Merapikan alur dan tampilan agar pengunjung menemukan yang mereka cari.', 'design'],
                    ['seo-maintenance', 'SEO, performa & perawatan', 'Menjaga website tetap cepat, sehat, mudah ditemukan, dan siap berkembang.', 'performance'],
                    ['education-training', 'Website pendidikan', 'Memperkenalkan kelas, program, dan materi belajar dengan alur yang mudah diikuti.', 'education'],
                    ['ai-automation', 'Otomasi AI', 'Mengurangi pekerjaan berulang dengan alur otomatis yang sesuai kebutuhan tim.', 'automation'],
                ] as $service)
                    <a href="{{ route('services.show', $service[0]) }}" class="home-service group">
                        <span class="home-service-icon"><x-line-icon :name="$service[3]"/></span>
                        <span class="home-service-copy"><strong>{{ $service[1] }}</strong><span>{{ $service[2] }}</span></span>
                        <span class="home-service-arrow"><x-arrow-icon/></span>
                    </a>
                @endforeach
            </div>
            <div class="home-stack"><span class="eyebrow">Teknologi yang kami gunakan</span><div class="home-stack-list">@foreach(['Laravel','PHP','JavaScript','Tailwind CSS','MySQL','SQLite','Alpine.js','Git','Figma'] as $tool)<span>{{ $tool }}</span>@endforeach</div></div>
        </div>
    </section>

    <section class="section-space home-regions border-y border-white/10 bg-[#151B21]">
        <div class="wrap">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <div><p class="eyebrow">Dari Malang, menjangkau Indonesia</p><h2 class="section-title mt-4">LAYANAN DIGITAL<br><span class="text-emerald-400">DI WILAYAHMU.</span></h2></div>
                <div class="max-w-lg"><p class="text-base leading-7 text-[#A1A4AD]">Kenali layanan Ryo Digital Website berdasarkan wilayah. Temukan informasi tentang kebutuhan website untuk UMKM, bisnis, lembaga, dan penyedia jasa di daerahmu.</p><p class="mt-4 text-xs font-bold uppercase tracking-widest text-[#A1A4AD]">{{ number_format($provinceCount) }} provinsi <span class="px-1 text-emerald-400">·</span> {{ number_format($cityCount) }} kabupaten/kota <span class="px-1 text-emerald-400">·</span> {{ number_format($districtCount) }} kecamatan</p></div>
            </div>
            <div class="mt-9 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($featuredProvinces as $province)
                    <a class="group flex items-center justify-between border-t border-white/15 py-4 text-base font-semibold text-[#F3F4F2] transition hover:border-emerald-400/60 hover:text-emerald-300" href="{{ route('regions.province', $province['slug']) }}"><span>{{ $province['name'] }}</span><span class="text-emerald-400"><x-arrow-icon/></span></a>
                @endforeach
            </div>
            <a class="btn btn-quiet mt-6" href="{{ route('regions.index') }}">Jelajahi semua wilayah <x-arrow-icon/></a>
        </div>
    </section>

    <section class="section-space home-process">
        <div class="wrap">
            <div class="home-process-heading"><div><p class="eyebrow">Kerja yang terbuka dan terarah</p><h2 class="section-title">DARI OBROLAN<br><span class="text-emerald-400">JADI KENYATAAN.</span></h2></div><p>Kamu selalu tahu apa yang sedang kami kerjakan, kenapa itu penting, dan apa langkah selanjutnya.</p></div>
            <div class="process-steps grid gap-7 md:grid-cols-5">
                @foreach([['01','Dengar','Kami pahami tujuan, pelanggan, dan tantangan bisnismu.','discover'],['02','Rancang arah','Kita sepakati ruang lingkup, prioritas, dan cara mengukur progres.','strategy'],['03','Bentuk ide','Alur dan visual dirancang sebelum masuk ke tahap pembangunan.','craft'],['04','Bangun','Website dikembangkan, diuji, dan dirapikan bersama.','develop'],['05','Luncurkan','Kami bantu website siap digunakan dan mendampingi langkah berikutnya.','launch']] as $step)
                    <div class="process-step reveal"><div class="process-topline"><span class="process-number">{{ $step[0] }}</span><span class="process-icon"><x-line-icon :name="$step[3]"/></span></div><h3>{{ $step[1] }}</h3><p>{{ $step[2] }}</p></div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="wrap home-contact">
        <div class="home-contact-panel reveal">
            <div class="home-contact-orbit" aria-hidden="true"></div>
            <p class="eyebrow">Ada ide yang ingin diwujudkan?</p>
            <h2 class="display">MARI BIKIN<br><span class="text-emerald-400">SESUATU YANG</span><br>BERARTI.</h2>
            <div class="home-contact-bottom"><p>Mulai dengan satu obrolan. Ceritakan bisnismu, kami bantu temukan bentuk digital yang paling masuk akal.</p><a class="btn btn-primary" href="{{ route('contact') }}">Mulai percakapan <x-arrow-icon/></a></div>
            <div class="home-contact-links"><a href="https://wa.me/6285648983077" target="_blank" rel="noreferrer">WhatsApp <span>085648983077</span></a>@if($siteSettings['email'] ?? config('ryo.email'))<a href="mailto:{{ $siteSettings['email'] ?? config('ryo.email') }}">Email <span>{{ $siteSettings['email'] ?? config('ryo.email') }}</span></a>@endif</div>
        </div>
    </section>

    @if($articles->isNotEmpty())
        <section class="section-space home-journal">
            <div class="wrap">
                <x-section-heading eyebrow="Catatan dan sudut pandang" title="Dari jurnal Ryo." link="Baca jurnal" :href="route('blog')"/>
                <div class="grid gap-5 md:grid-cols-3">
                    @foreach($articles as $article)
                        <a href="{{ route('blog.show', $article->slug) }}" class="home-journal-card"><span class="eyebrow">{{ $article->category?->name ?? 'Jurnal' }} · {{ $article->published_at?->format('d M Y') }}</span><h3>{{ $article->title }}</h3><p>{{ $article->excerpt }}</p><span class="home-journal-link">LANJUT BACA <x-arrow-icon/></span></a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
@endsection
