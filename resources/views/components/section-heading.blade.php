@props(['eyebrow', 'title', 'link' => null, 'href' => null])
<div class="mb-10 flex flex-col justify-between gap-5 sm:flex-row sm:items-end"><div><p class="eyebrow mb-4">{{ $eyebrow }}</p><h2 class="section-title">{{ $title }}</h2></div>@if($link)<a href="{{ $href }}" class="nav-link inline-flex items-center gap-3">{{ $link }} <x-arrow-icon class="text-emerald-400"/></a>@endif</div>
