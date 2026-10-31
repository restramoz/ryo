<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach(['home','about','services','pricing','portfolio','blog','contact'] as $name)<url><loc>{{ route($name) }}</loc><changefreq>monthly</changefreq><priority>{{ $name==='home'?'1.0':'0.7' }}</priority></url>@endforeach
@foreach(config('ryo.services') as $slug=>$service)<url><loc>{{ route('services.show',$slug) }}</loc><changefreq>monthly</changefreq><priority>0.6</priority></url>@endforeach
@foreach($provinces as $province)<url><loc>{{ route('regions.province',$province['slug']) }}</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>@endforeach
@foreach($cities as $city)<url><loc>{{ route('regions.city',$city['slug']) }}</loc><changefreq>monthly</changefreq><priority>0.45</priority></url>@endforeach
@foreach($districts as $district)<url><loc>{{ route('regions.district',[$district['city_slug'],$district['slug']]) }}</loc><changefreq>monthly</changefreq><priority>0.35</priority></url>@endforeach
@foreach($regionalPages as $regionalPage)<url><loc>{{ route('regional.show',[$regionalPage->target_city_slug,$regionalPage->service_slug]) }}</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>@endforeach
@foreach(config('ryo.projects') as $slug=>$project)<url><loc>{{ route('portfolio.show',$slug) }}</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>@endforeach
@foreach($articles as $article)<url><loc>{{ route('blog.show',$article->slug) }}</loc><lastmod>{{ $article->updated_at->toAtomString() }}</lastmod><changefreq>monthly</changefreq><priority>0.6</priority></url>@endforeach
@foreach($categories as $category)<url><loc>{{ route('blog',['category'=>$category->slug]) }}</loc><changefreq>weekly</changefreq><priority>0.4</priority></url>@endforeach
@foreach($tags as $tag)<url><loc>{{ route('blog',['tag'=>$tag->slug]) }}</loc><changefreq>weekly</changefreq><priority>0.3</priority></url>@endforeach
</urlset>
