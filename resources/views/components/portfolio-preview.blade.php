@props(['project'])

@php($projectHost = parse_url($project['url'], PHP_URL_HOST))

<article class="portfolio-preview-card">
    <div class="portfolio-browser">
        <div class="portfolio-browser-bar">
            <span class="portfolio-browser-dots" aria-hidden="true"><i></i><i></i><i></i></span>
            <span class="portfolio-browser-address">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="1.5"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                {{ $projectHost }}
            </span>
            <a class="portfolio-browser-open" href="{{ $project['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="Buka website {{ $project['title'] }} di tab baru">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M14 4h6v6m-10 4L20 4"/><path d="M18 13v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h6"/></svg>
            </a>
        </div>

        @if($project['embed_allowed'])
            <div class="portfolio-browser-screen">
                <iframe src="{{ $project['url'] }}" title="Preview langsung website {{ $project['title'] }}" loading="lazy" referrerpolicy="no-referrer" sandbox="allow-forms allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox"></iframe>
            </div>
        @else
            <div class="portfolio-browser-screen portfolio-preview-restricted">
                <span class="portfolio-restricted-mark" aria-hidden="true"><x-line-icon name="website"/></span>
                <p class="eyebrow">Preview langsung terbatas</p>
                <p>Website ini membatasi penayangan di dalam frame. Buka situs untuk melihat tampilannya secara utuh.</p>
                <a class="btn btn-primary" href="{{ $project['url'] }}" target="_blank" rel="noopener noreferrer">Buka {{ $project['title'] }} <x-arrow-icon/></a>
            </div>
        @endif
    </div>

    <div class="portfolio-preview-details">
        <div>
            <p class="eyebrow">{{ $project['category'] }}</p>
            <h2>{{ $project['title'] }}</h2>
            <p class="portfolio-preview-description">{{ $project['description'] }}</p>
        </div>
        <a class="portfolio-preview-visit" href="{{ $project['url'] }}" target="_blank" rel="noopener noreferrer">Kunjungi website <x-arrow-icon/></a>
    </div>
</article>
