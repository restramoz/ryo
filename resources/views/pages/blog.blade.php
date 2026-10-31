@extends('layouts.app')

@section('content')
<div class="journal-page">
    <header class="wrap section-space pb-10 pt-36 md:pb-14">
        <p class="eyebrow">The Ryo Digital Website journal</p>
        <div class="mt-5 grid gap-6 lg:grid-cols-[1fr_minmax(250px,380px)] lg:items-end">
            <h1 class="display text-[clamp(52px,9vw,112px)]">THINKING<br><span class="text-emerald-400">DIGITALLY.</span></h1>
            <p class="max-w-md text-base leading-7 text-[#A1A4AD]">Perspektif, panduan, dan catatan praktis tentang website serta pengalaman digital untuk bisnis.</p>
        </div>

        <form class="journal-filters mt-9 grid gap-3 border-y border-white/10 py-4 md:grid-cols-[minmax(220px,1.4fr)_minmax(150px,.8fr)_minmax(150px,.8fr)_auto]" method="GET" role="search" aria-label="Cari artikel">
            <label class="sr-only" for="journal-search">Search articles</label>
            <div class="relative">
                <svg class="pointer-events-none absolute left-4 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[#A1A4AD]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.5 4.5"/></svg>
                <input id="journal-search" class="field h-12 !pl-11" name="q" value="{{ request('q') }}" placeholder="Search articles">
            </div>
            <label class="sr-only" for="journal-category">Filter by category</label>
            <select id="journal-category" class="field h-12" name="category"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->slug }}" @selected(request('category')===$category->slug)>{{ $category->name }}</option>@endforeach</select>
            <label class="sr-only" for="journal-tag">Filter by tag</label>
            <select id="journal-tag" class="field h-12" name="tag"><option value="">All tags</option>@foreach($tags as $tag)<option value="{{ $tag->slug }}" @selected(request('tag')===$tag->slug)>{{ $tag->name }}</option>@endforeach</select>
            <button class="btn btn-primary h-12 min-h-12 px-6" type="submit">Filter <x-arrow-icon/></button>
        </form>
        @if(request()->filled('q') || request()->filled('category') || request()->filled('tag'))
            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-sm text-[#A1A4AD]"><p>{{ $articles->total() }} artikel ditemukan</p><a class="font-semibold text-emerald-300 hover:text-emerald-200" href="{{ route('blog') }}">Hapus filter <span aria-hidden="true">×</span></a></div>
        @else
            <p class="mt-4 text-sm text-[#A1A4AD]">{{ $articles->total() }} artikel untuk dibaca</p>
        @endif
    </header>

    <section class="wrap pb-20" aria-label="Daftar artikel">
        <div class="journal-grid grid items-stretch gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @forelse($articles as $article)
                <article class="journal-card group">
                    <div class="journal-card-media">
                        @if($article->featured_image)
                            <img src="{{ app(\App\Services\PublicUploadService::class)->url($article->featured_image) }}" alt="{{ $article->image_alt ?: $article->title }}" loading="lazy" decoding="async">
                        @else
                            <div class="journal-card-art project-art art-{{ (($article->id - 1) % 3) + 1 }}"></div>
                        @endif
                        <span class="journal-card-open"><x-arrow-icon/></span>
                    </div>
                    <div class="journal-card-body">
                        <div class="journal-card-meta"><span>{{ $article->category?->name ?? 'Journal' }}</span><span class="journal-meta-dot">·</span><time datetime="{{ $article->published_at?->toAtomString() }}">{{ $article->published_at?->format('d M Y') }}</time></div>
                        <h2><a href="{{ route('blog.show',$article->slug) }}">{{ $article->title }}</a></h2>
                        <p class="journal-card-excerpt">{{ $article->excerpt }}</p>
                        <a class="journal-card-link" href="{{ route('blog.show',$article->slug) }}">Baca artikel <x-arrow-icon/></a>
                    </div>
                </article>
            @empty
                <div class="journal-empty sm:col-span-2 xl:col-span-3">
                    <span class="eyebrow">Belum ada hasil</span>
                    <h2 class="mt-3 font-display text-2xl uppercase">Coba kata kunci atau filter lain.</h2>
                    <p class="mt-2 text-sm leading-6 text-[#A1A4AD]">Kami belum menemukan artikel yang cocok dengan pilihanmu.</p>
                    <a class="btn btn-quiet mt-5" href="{{ route('blog') }}">Lihat semua artikel</a>
                </div>
            @endforelse
        </div>
        @if($articles->hasPages())<div class="journal-pagination mt-10">{{ $articles->links() }}</div>@endif
    </section>
</div>
@endsection
