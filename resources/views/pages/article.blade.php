@extends('layouts.app', ['title' => ($article->meta_title ?: $article->title).' · Ryo Digital Website', 'description' => $article->meta_description ?: $article->excerpt, 'canonical' => $article->canonical_url ?: rtrim(config('app.url'), '/').'/blog/'.$article->slug, 'ogImage' => $article->og_image ?: ($article->featured_image ? app(\App\Services\PublicUploadService::class)->url($article->featured_image) : null), 'ogType' => 'article'])

@section('content')
<article class="article-page">
    <header class="article-header wrap section-space pt-32 pb-10 lg:pt-40 lg:pb-14">
        <nav class="mb-9 flex flex-wrap items-center gap-2 text-sm text-[#A1A4AD]" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-400">Beranda</a><span class="text-emerald-400">/</span>
            <a href="{{ route('blog') }}" class="hover:text-emerald-400">Jurnal</a><span class="text-emerald-400">/</span>
            <span aria-current="page" class="text-[#F3F4F2]">{{ $article->title }}</span>
        </nav>
        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_230px] lg:items-end">
            <div>
                <p class="eyebrow">{{ $article->category?->name ?? 'Jurnal Ryo' }} <span class="px-1 text-emerald-400">·</span> {{ $article->published_at?->translatedFormat('d F Y') }}</p>
                <h1 class="display mt-5 max-w-5xl text-[clamp(44px,7vw,92px)] leading-[.94]">{{ strtoupper($article->title) }}</h1>
                @if($article->excerpt)<p class="mt-6 max-w-3xl text-lg leading-8 text-[#A1A4AD]">{{ $article->excerpt }}</p>@endif
            </div>
            <div class="border-l border-emerald-400/40 pl-5 text-sm leading-6 text-[#A1A4AD]">
                <span class="eyebrow">Disusun oleh</span>
                <p class="mt-2 font-semibold text-[#F3F4F2]">{{ $article->author?->name ?? 'Ryo Digital Website' }}</p>
                <p class="mt-1">Insight untuk bisnis yang ingin melangkah dengan arah digital yang jelas.</p>
            </div>
        </div>
        @if($article->service_slug && $article->target_city_slug && ($regionalCity = app(\App\Services\RegionalService::class)->findCity($article->target_city_slug)))
            <a class="mt-6 inline-flex items-center gap-2 border border-emerald-400/30 px-3 py-2 text-sm text-emerald-300 hover:bg-emerald-400/10" href="{{ route('regional.show',[$article->target_city_slug,$article->service_slug]) }}">{{ config('ryo.services.'.$article->service_slug.'.title') }} · {{ $regionalCity['name'] }} <x-arrow-icon/></a>
        @endif
    </header>

    @if($article->featured_image)
        <div class="wrap"><figure class="article-featured-image"><img src="{{ app(\App\Services\PublicUploadService::class)->url($article->featured_image) }}" alt="{{ $article->image_alt ?: $article->title }}" fetchpriority="high"><figcaption>{{ $article->image_alt ?: $article->title }}</figcaption></figure></div>
    @endif

    <div class="wrap grid gap-12 pb-20 pt-10 lg:grid-cols-[minmax(0,790px)_minmax(220px,1fr)] lg:gap-16 lg:pt-14">
        <div class="min-w-0">
            <div class="article-content article-content-editorial">{!! $article->safeContentHtml() !!}</div>
            @if($article->tags->isNotEmpty())
                <div class="mt-12 flex flex-wrap gap-2 border-t border-white/10 pt-6" aria-label="Tag artikel">
                    @foreach($article->tags as $tag)<a class="border border-white/10 px-3 py-2 text-sm text-[#A1A4AD] transition hover:border-emerald-400/40 hover:text-emerald-300" href="{{ route('blog',['tag'=>$tag->slug]) }}">#{{ $tag->name }}</a>@endforeach
                </div>
            @endif
        </div>

        <aside class="space-y-5 lg:sticky lg:top-28 lg:self-start">
            <div class="border border-emerald-400/25 bg-[#171D25] p-5 sm:p-6">
                <p class="eyebrow">Punya kebutuhan serupa?</p>
                <h2 class="mt-3 font-display text-2xl uppercase leading-tight">Bicarakan arah digital bisnismu.</h2>
                <p class="mt-3 text-sm leading-6 text-[#A1A4AD]">Ceritakan konteks dan tujuanmu. Ryo Digital Website akan membantu mencari langkah yang relevan.</p>
                <a class="btn btn-primary mt-5 w-full" href="{{ route('contact') }}">Ceritakan proyekmu <x-arrow-icon/></a>
            </div>
            @if($related->isNotEmpty())
                <div class="border-t border-white/15 pt-5"><p class="eyebrow">Baca selanjutnya</p><div class="mt-3 divide-y divide-white/10">
                    @foreach($related as $item)<a class="group flex items-start justify-between gap-3 py-4 text-sm font-semibold leading-5 text-[#F3F4F2] hover:text-emerald-300" href="{{ route('blog.show',$item->slug) }}"><span>{{ $item->title }}</span><x-arrow-icon/></a>@endforeach
                </div></div>
            @endif
        </aside>
    </div>
</article>
@endsection

@push('structured-data')<script type="application/ld+json">{!! json_encode(['@context'=>'https://schema.org','@type'=>'Article','headline'=>$article->title,'description'=>$article->meta_description ?: $article->excerpt,'datePublished'=>$article->published_at?->toAtomString(),'dateModified'=>$article->updated_at->toAtomString(),'author'=>['@type'=>'Organization','name'=>$article->author?->name ?? 'Ryo Digital Website'],'image'=>$article->og_image ?: ($article->featured_image ? app(\App\Services\PublicUploadService::class)->url($article->featured_image) : null),'mainEntityOfPage'=>rtrim(config('app.url'), '/').'/blog/'.$article->slug], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>@endpush
@push('structured-data')<script type="application/ld+json">{!! json_encode(['@context'=>'https://schema.org','@type'=>'BreadcrumbList','itemListElement'=>[['@type'=>'ListItem','position'=>1,'name'=>'Beranda','item'=>route('home')],['@type'=>'ListItem','position'=>2,'name'=>'Jurnal','item'=>route('blog')],['@type'=>'ListItem','position'=>3,'name'=>$article->title,'item'=>route('blog.show',$article->slug)]]], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>@endpush
