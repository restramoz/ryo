<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Services\RegionalService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function home(RegionalService $regionalService): View
    {
        $featuredProvinceNames = ['Jawa Timur', 'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'Bali', 'Sumatera Utara', 'Kalimantan Timur', 'Sulawesi Selatan'];
        $provincesByName = collect($regionalService->getAllProvinces())->keyBy('name');
        $featuredProvinces = collect($featuredProvinceNames)->map(fn (string $name) => $provincesByName->get($name))->filter()->values();

        return view('pages.home', [
            'articles' => Article::published()->latest('published_at')->limit(3)->get(),
            'featuredProvinces' => $featuredProvinces,
            'provinceCount' => count($regionalService->getAllProvinces()),
            'cityCount' => count($regionalService->getAllCities()),
            'districtCount' => array_sum(array_map(fn (array $city): int => count($regionalService->getDistrictsByCity($city['slug'])), $regionalService->getAllCities())),
        ]);
    }

    public function page(string $page, RegionalService $regionalService): View
    {
        abort_unless(in_array($page, ['about', 'services', 'pricing', 'contact'], true), 404);

        $titles = ['about' => 'Tentang Ryo Digital Website — Studio Digital untuk Bisnis Indonesia', 'services' => 'Layanan Digital untuk Bisnis', 'pricing' => 'Harga & Estimasi Proyek', 'contact' => 'Hubungi Ryo Digital Website'];

        return view('pages.'.$page, [
            'provinces' => $page === 'services' ? $regionalService->getAllProvinces() : [],
            'title' => in_array($page, ['about', 'contact'], true) ? $titles[$page] : $titles[$page].' · Ryo Digital Website',
            'description' => match ($page) {
                'about' => 'Kenali cara kerja, visi, dan misi Ryo Digital Website dalam membantu bisnis Indonesia membangun pengalaman digital yang jelas dan berguna.',
                'services' => 'Jelajahi layanan website, aplikasi web, e-commerce, desain UI/UX, SEO, dan otomasi yang disesuaikan dengan kebutuhan bisnismu.',
                'pricing' => 'Pahami cara Ryo Digital Website menyusun estimasi proyek website berdasarkan ruang lingkup, fitur, dan kebutuhan bisnismu.',
                'contact' => 'Ceritakan kebutuhan website atau proyek digital bisnismu kepada Ryo Digital Website. Mulai percakapan melalui formulir WhatsApp atau kontak yang tersedia.',
                default => null,
            },
        ]);
    }

    public function service(string $slug): View
    {
        $services = config('ryo.services');
        abort_unless(isset($services[$slug]), 404);

        return view('pages.service', ['service' => $services[$slug], 'slug' => $slug, 'title' => $services[$slug]['title'].' · Ryo Digital Website', 'description' => $services[$slug]['description']]);
    }

    public function portfolio(): View
    {
        return view('pages.portfolio', [
            'title' => 'Portfolio Website & Pengalaman Digital · Ryo Digital Website',
            'description' => 'Jelajahi portfolio website Ryo Digital Website untuk TrainingKota, Najkaltare, dan Chifheto. Lihat preview langsung dan buka setiap website.',
            'projects' => config('ryo.projects'),
        ]);
    }

    public function project(string $slug): View
    {
        $projects = config('ryo.projects');
        abort_unless(isset($projects[$slug]), 404);

        return view('pages.project', [
            'slug' => $slug,
            'project' => $projects[$slug],
            'title' => $projects[$slug]['title'].' · Portfolio Ryo Digital Website',
            'description' => $projects[$slug]['description'],
        ]);
    }

    public function blog(Request $request): View
    {
        $articles = Article::published()->with(['category', 'tags', 'author'])
            ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('category'), fn ($query) => $query->whereHas('category', fn ($category) => $category->where('slug', $request->string('category'))))
            ->when($request->filled('tag'), fn ($query) => $query->whereHas('tags', fn ($tag) => $tag->where('slug', $request->string('tag'))))
            ->latest('published_at')->paginate(9)->withQueryString();

        return view('pages.blog', ['articles' => $articles, 'categories' => Category::orderBy('name')->get(), 'tags' => Tag::orderBy('name')->get(), 'title' => 'Digital Journal · Ryo Digital Website']);
    }

    public function article(Article $article): View
    {
        abort_unless($article->status === 'published' && $article->published_at?->isPast(), 404);
        $article->load(['category', 'tags', 'author']);
        $related = Article::published()->where('id', '!=', $article->id)->when($article->category_id, fn ($query) => $query->where('category_id', $article->category_id))->latest('published_at')->limit(3)->get();

        return view('pages.article', compact('article', 'related'));
    }
}
