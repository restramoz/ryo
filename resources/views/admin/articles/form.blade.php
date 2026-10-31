@extends('layouts.admin')

@php
    $regionalService = app(\App\Services\RegionalService::class);
    $articleContent = old('content', $article->exists ? $article->safeContentHtml() : '');
    $selectedCity = old('target_city_slug') ? ($selectedCity && $selectedCity['slug'] === old('target_city_slug') ? $selectedCity : $regionalService->findCity(old('target_city_slug'))) : $selectedCity;
    $regionalDistricts = $selectedCity ? $regionalService->getDistrictsByCity($selectedCity['slug']) : $regionalDistricts;
    $selectedProvinceSlug = old('target_province_slug', $selectedProvinceSlug ?: ($selectedCity['province_slug'] ?? ''));
    $selectedRegionScope = old('target_region_scope', $article->target_region_scope ?: ($article->target_city_slug ? 'city' : 'all'));
    $selectedDistrictSlug = old('target_district_slug', $selectedDistrict['slug'] ?? '');
@endphp

@section('content')
<div class="flex flex-wrap items-end justify-between gap-4">
    <div><p class="eyebrow">Studio journal · SEO articles</p><h1 class="display mt-3 text-5xl">{{ $article->exists ? 'EDIT ARTICLE' : 'NEW ARTICLE' }}</h1></div>
    <a href="{{ route('admin.articles.index') }}" class="nav-link">← Kembali ke artikel</a>
</div>

<section class="mt-8 border border-emerald-400/25 bg-[#171D25] p-5 sm:p-7" data-admin-article-form
    data-generate-url="{{ route('admin.articles.ai-generate') }}"
    data-image-prompt-url="{{ route('admin.articles.image-prompt') }}"
    data-upload-url="{{ route('admin.articles.upload-image') }}"
    data-cities-url="{{ route('admin.regional-cities') }}"
    data-districts-url="{{ route('admin.regional-districts') }}">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div><p class="eyebrow">Ollama writing assistant</p><h2 class="mt-2 font-display text-2xl uppercase">Generate artikel terarah</h2><p class="mt-2 max-w-2xl text-sm leading-6 text-[#A5ADB5]">Buat draf SEO berbahasa Indonesia dengan konteks layanan Ryo Digital Website dan wilayah pilihan dari master BPS. Periksa kembali fakta dan hasil sebelum diterbitkan.</p></div>
        <span class="border px-3 py-2 text-xs font-bold uppercase tracking-widest {{ $ollamaConfigured ? 'border-emerald-400/30 bg-emerald-400/10 text-emerald-300' : 'border-white/10 text-[#A5ADB5]' }}">{{ $ollamaConfigured ? 'Ollama siap' : 'Ollama belum diatur' }}</span>
    </div>
    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <label class="text-xs font-semibold uppercase tracking-wider text-[#A5ADB5] xl:col-span-2">Topik artikel<input class="field mt-2" data-article-topic placeholder="Contoh: Panduan memilih website untuk lembaga pelatihan"></label>
        <label class="text-xs font-semibold uppercase tracking-wider text-[#A5ADB5]">Layanan<select class="field mt-2" data-service-select><option value="">Pilih layanan (opsional)</option>@foreach($services as $slug => $service)<option value="{{ $slug }}" @selected(old('service_slug', $article->service_slug) === $slug)>{{ $service['title'] }}</option>@endforeach</select></label>
        <label class="text-xs font-semibold uppercase tracking-wider text-[#A5ADB5]">Target panjang<input class="field mt-2" type="number" min="600" max="10000" step="100" value="1200" data-word-count><span class="mt-2 block normal-case tracking-normal">600–10.000 kata</span></label>
        <label class="text-xs font-semibold uppercase tracking-wider text-[#A5ADB5]">Cakupan wilayah<select class="field mt-2" name="target_region_scope" data-region-scope><option value="all" @selected($selectedRegionScope === 'all')>Semua wilayah</option><option value="province" @selected($selectedRegionScope === 'province')>Provinsi tertentu</option><option value="city" @selected($selectedRegionScope === 'city')>Kota / kabupaten tertentu</option><option value="district" @selected($selectedRegionScope === 'district')>Kecamatan tertentu</option></select></label>
        <p class="self-end text-sm leading-6 text-[#A5ADB5]">Artikel “Semua wilayah” tampil di seluruh halaman provinsi, kota/kabupaten, dan kecamatan. Pilihan provinsi atau kota juga mencakup wilayah di bawahnya.</p>
        <label class="hidden text-xs font-semibold uppercase tracking-wider text-[#A5ADB5] md:col-span-2" data-region-province>Provinsi<select class="field mt-2" data-province-select name="target_province_slug"><option value="">Pilih provinsi</option>@foreach($provinces as $province)<option value="{{ $province['slug'] }}" @selected($selectedProvinceSlug === $province['slug'])>{{ $province['name'] }}</option>@endforeach</select></label>
        <div class="hidden text-xs font-semibold uppercase tracking-wider text-[#A5ADB5] md:col-span-2" data-region-city><label for="target-city-select">Kota / kabupaten</label>
            <div class="mt-2 grid gap-2 sm:grid-cols-[minmax(0,1fr)_minmax(0,1.2fr)]"><input class="field" data-city-search placeholder="Cari kota atau kabupaten…" aria-label="Cari kota atau kabupaten"><select class="field" id="target-city-select" data-city-select name="target_city_slug"><option value="">Pilih kota / kabupaten</option>@if($selectedCity)<option value="{{ $selectedCity['slug'] }}" selected>{{ $selectedCity['name'] }} · {{ $selectedCity['province'] }}</option>@endif</select></div>
        </div>
        <div class="hidden text-xs font-semibold uppercase tracking-wider text-[#A5ADB5] md:col-span-2" data-region-district><label for="target-district-select">Kecamatan</label>
            <div class="mt-2 grid gap-2 sm:grid-cols-[minmax(0,1fr)_minmax(0,1.2fr)]"><input class="field" data-district-search placeholder="Cari kecamatan…" aria-label="Cari kecamatan"><select class="field" id="target-district-select" data-district-select name="target_district_slug"><option value="">Pilih kecamatan</option>@foreach($regionalDistricts as $district)<option value="{{ $district['slug'] }}" @selected($selectedDistrictSlug === $district['slug'])>{{ $district['name'] }}</option>@endforeach</select></div>
        </div>
        <p class="text-xs leading-5 text-[#A5ADB5] md:col-span-2">Data wilayah bersumber dari master BPS. Artikel regional tampil setelah diterbitkan.</p>
    </div>
    <div class="mt-5 flex flex-wrap items-center gap-4"><button type="button" data-generate-article class="btn btn-primary" @disabled(!$ollamaConfigured)>Generate artikel <x-arrow-icon/></button><span data-generator-status role="status" aria-live="polite" class="text-sm text-[#A5ADB5]">{{ $ollamaConfigured ? 'Hasil akan dimasukkan sebagai draf dan dapat kamu sunting.' : 'Atur OLLAMA_BASE_URL dan OLLAMA_MODEL untuk mengaktifkan generator.' }}</span></div>
</section>

<form class="mt-7 space-y-6" method="POST" enctype="multipart/form-data" action="{{ $article->exists ? route('admin.articles.update',$article) : route('admin.articles.store') }}" data-article-save-form>
    @csrf @if($article->exists)@method('PUT')@endif
    <input type="hidden" name="is_ai_generated" data-ai-generated value="{{ old('is_ai_generated', $article->is_ai_generated ? '1' : '0') }}">
    <section class="grid gap-5 border border-white/10 bg-[#171D25] p-5 sm:p-7 lg:grid-cols-2">
        <label class="text-xs font-semibold uppercase tracking-wider text-[#A5ADB5] lg:col-span-2">Judul artikel<input class="field mt-2" name="title" data-article-title value="{{ old('title',$article->title) }}" required maxlength="255"></label>
        <label class="text-xs font-semibold uppercase tracking-wider text-[#A5ADB5]">Slug<input class="field mt-2" name="slug" value="{{ old('slug',$article->slug) }}" placeholder="Otomatis dari judul jika kosong"></label>
        <label class="text-xs font-semibold uppercase tracking-wider text-[#A5ADB5]">Kategori<select class="field mt-2" name="category_id"><option value="">Tanpa kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id',$article->category_id)==$category->id)>{{ $category->name }}</option>@endforeach</select></label>
        <label class="text-xs font-semibold uppercase tracking-wider text-[#A5ADB5] lg:col-span-2">Ringkasan<textarea class="field mt-2 min-h-24" name="excerpt" data-article-excerpt maxlength="1000">{{ old('excerpt',$article->excerpt) }}</textarea></label>
        <div class="lg:col-span-2"><label for="content" class="text-xs font-semibold uppercase tracking-wider text-[#A5ADB5]">Konten artikel</label><textarea id="content" name="content" data-jodit-content>{{ $articleContent }}</textarea></div>
        <div class="border-t border-white/10 pt-5 lg:col-span-2">
            <div class="flex flex-wrap items-end justify-between gap-4"><div><p class="eyebrow">Image studio</p><h2 class="mt-2 font-display text-xl uppercase">Prompt gambar artikel</h2></div><button type="button" data-generate-image-prompt class="btn btn-quiet" @disabled(!$ollamaConfigured)>Buat prompt gambar</button></div>
            <textarea class="field mt-4 min-h-28" name="image_prompt" data-image-prompt maxlength="3000" placeholder="Prompt visual editorial untuk generator gambar">{{ old('image_prompt',$article->image_prompt) }}</textarea>
            <div class="mt-3 flex flex-wrap items-center gap-4"><button type="button" data-copy-image-prompt class="nav-link">Salin prompt</button><span data-image-prompt-status role="status" aria-live="polite" class="text-xs text-[#A5ADB5]"></span></div>
        </div>
    </section>

    <section class="grid gap-5 border border-white/10 bg-[#171D25] p-5 sm:p-7 lg:grid-cols-2">
        <p class="eyebrow lg:col-span-2">Publication & search engine optimization</p>
        <label class="text-xs font-semibold uppercase tracking-wider text-[#A5ADB5]">Status<select class="field mt-2" name="status">@foreach(['draft'=>'Draf','published'=>'Terbit','scheduled'=>'Terjadwal'] as $value=>$label)<option value="{{ $value }}" @selected(old('status',$article->status ?? 'draft')===$value)>{{ $label }}</option>@endforeach</select></label>
        <label class="text-xs font-semibold uppercase tracking-wider text-[#A5ADB5]">Tanggal publikasi<input class="field mt-2" type="datetime-local" name="published_at" value="{{ old('published_at',$article->published_at?->format('Y-m-d\TH:i')) }}"></label>
        <label class="text-xs font-semibold uppercase tracking-wider text-[#A5ADB5]">Kata kunci utama<input class="field mt-2" name="focus_keyword" value="{{ old('focus_keyword',$article->focus_keyword) }}"></label>
        <label class="text-xs font-semibold uppercase tracking-wider text-[#A5ADB5]">Meta title<input class="field mt-2" name="meta_title" value="{{ old('meta_title',$article->meta_title) }}" maxlength="255"></label>
        <label class="text-xs font-semibold uppercase tracking-wider text-[#A5ADB5] lg:col-span-2">Meta description<textarea class="field mt-2 min-h-20" name="meta_description" maxlength="500">{{ old('meta_description',$article->meta_description) }}</textarea></label>
        <label class="text-xs font-semibold uppercase tracking-wider text-[#A5ADB5]">Canonical URL<input class="field mt-2" type="url" name="canonical_url" value="{{ old('canonical_url',$article->canonical_url) }}"></label>
        <label class="text-xs font-semibold uppercase tracking-wider text-[#A5ADB5]">Open Graph image URL<input class="field mt-2" type="url" name="og_image" value="{{ old('og_image',$article->og_image) }}"></label>
        <label class="text-xs font-semibold uppercase tracking-wider text-[#A5ADB5]">Gambar utama<input class="field mt-2" type="file" name="featured_image" accept="image/jpeg,image/png,image/webp">@if($article->featured_image)<span class="mt-2 block normal-case tracking-normal">Gambar tersimpan: {{ basename($article->featured_image) }}</span>@endif</label>
        <label class="text-xs font-semibold uppercase tracking-wider text-[#A5ADB5]">Alt text gambar<input class="field mt-2" name="image_alt" value="{{ old('image_alt',$article->image_alt) }}" maxlength="255"></label>
        <label class="text-xs font-semibold uppercase tracking-wider text-[#A5ADB5] lg:col-span-2">Tag<select class="field mt-2 min-h-28" name="tags[]" multiple>@foreach($tags as $tag)<option value="{{ $tag->id }}" @selected(collect(old('tags',$article->exists ? $article->tags->pluck('id')->all() : []))->contains($tag->id))>{{ $tag->name }}</option>@endforeach</select><span class="mt-2 block normal-case tracking-normal">Tahan Ctrl / Command untuk memilih beberapa tag.</span></label>
    </section>
    <div class="flex flex-wrap items-center gap-3"><button class="btn btn-primary">{{ $article->exists ? 'Simpan perubahan' : 'Simpan artikel' }} <x-arrow-icon/></button>@if($article->exists)<button type="button" data-modal-target="delete-article-modal" data-modal-toggle="delete-article-modal" class="btn btn-quiet">Hapus artikel</button>@endif</div>
</form>

@if($article->exists)
<div id="delete-article-modal" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-black/70">
    <div class="relative w-full max-w-md p-4"><div class="border border-white/10 bg-[#1D252E] p-6"><h2 class="font-display text-2xl uppercase">Hapus artikel?</h2><p class="mt-3 text-sm text-[#A5ADB5]">Artikel “{{ $article->title }}” akan dihapus.</p><form class="mt-6 flex gap-3" method="POST" action="{{ route('admin.articles.destroy',$article) }}">@csrf @method('DELETE')<button class="btn border-red-400/40 text-red-200">Hapus artikel</button><button type="button" data-modal-hide="delete-article-modal" class="btn btn-quiet">Batal</button></form></div></div>
</div>
@endif
@endsection
