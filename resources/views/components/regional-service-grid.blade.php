@props(['services', 'regionName'])

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @foreach($services as $slug => $service)
        @php($message = 'Halo Ryo Digital Website, saya ingin konsultasi tentang '.$service['title'].' untuk bisnis di '.$regionName.'.')
        <article class="border border-white/10 bg-[#171D25] p-5 transition hover:border-emerald-400/35 sm:p-6">
            <span class="grid h-11 w-11 place-items-center border border-emerald-400/30 text-emerald-400"><x-line-icon :name="$service['icon']"/></span>
            <h3 class="mt-5 font-display text-xl uppercase leading-tight">{{ $service['title'] }}</h3>
            <p class="mt-3 min-h-[4.5rem] text-sm leading-6 text-[#A1A4AD]">{{ $service['description'] }}</p>
            <a class="mt-5 inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-emerald-300 hover:text-emerald-200" href="https://wa.me/6285648983077?text={{ rawurlencode($message) }}" target="_blank" rel="noopener noreferrer">Konsultasi · 085648983077 <x-arrow-icon/></a>
        </article>
    @endforeach
</div>
