@if($articles->isNotEmpty())
    <section class="border-t border-white/10 py-14 md:py-18" aria-labelledby="regional-articles-heading">
        <div class="wrap">
            <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="eyebrow">Jurnal wilayah</p>
                    <h2 id="regional-articles-heading" class="section-title mt-3">ARTIKEL UNTUK {{ strtoupper($regionName) }}.</h2>
                </div>
                <a href="{{ route('blog') }}" class="nav-link">Jelajahi jurnal <x-arrow-icon/></a>
            </div>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($articles as $article)
                    <a href="{{ route('blog.show', $article->slug) }}" class="group flex min-h-52 flex-col justify-between border border-white/10 bg-[#171D25] p-5 transition-colors hover:border-emerald-400/40 sm:p-6">
                        <div>
                            <span class="eyebrow">{{ $article->category?->name ?? 'Artikel' }} <span class="px-1 text-emerald-400">·</span> {{ $article->published_at?->translatedFormat('d M Y') }}</span>
                            <h3 class="mt-4 text-balance font-display text-xl uppercase leading-tight text-[#F3F4F2] group-hover:text-emerald-300">{{ $article->title }}</h3>
                            @if($article->excerpt)
                                <p class="mt-3 line-clamp-3 text-sm leading-6 text-[#A5ADB5]">{{ $article->excerpt }}</p>
                            @endif
                        </div>
                        <span class="mt-5 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-emerald-300">Baca artikel <x-arrow-icon/></span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
