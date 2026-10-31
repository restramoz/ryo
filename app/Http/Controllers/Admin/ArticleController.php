<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Services\HtmlSanitizerService;
use App\Services\OllamaService;
use App\Services\PublicUploadService;
use App\Services\RegionalService;
use App\Services\WhatsAppCtaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct(private readonly RegionalService $regionalService, private readonly PublicUploadService $uploads) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $articles = Article::with('category')->latest()->paginate(15);
        $articles->getCollection()->each(function (Article $article): void {
            $article->setAttribute('regional_target_label', $this->regionalTargetLabel($article));
        });

        return view('admin.articles.index', ['articles' => $articles]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(OllamaService $ollama): View
    {
        return view('admin.articles.form', [
            'article' => new Article,
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'services' => config('ryo.services'),
            'selectedCity' => null,
            'selectedProvinceSlug' => null,
            'selectedDistrict' => null,
            'regionalDistricts' => [],
            'provinces' => $this->regionalService->getAllProvinces(),
            'ollamaConfigured' => $ollama->isConfigured(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, HtmlSanitizerService $sanitizer, WhatsAppCtaService $whatsAppCtas): RedirectResponse
    {
        $data = $this->validated($request, $sanitizer, $whatsAppCtas);
        $article = Article::create($data);
        $article->tags()->sync($request->input('tags', []));

        return redirect()->route('admin.articles.edit', $article)->with('success', 'Article saved.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article): RedirectResponse
    {
        return redirect()->route('admin.articles.edit', $article);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article, OllamaService $ollama): View
    {
        $selectedCity = $article->target_city_slug ? $this->regionalService->findCity($article->target_city_slug) : null;

        return view('admin.articles.form', [
            'article' => $article->load('tags'),
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'services' => config('ryo.services'),
            'selectedCity' => $selectedCity,
            'selectedProvinceSlug' => $article->target_province_slug ?: ($selectedCity['province_slug'] ?? null),
            'selectedDistrict' => $article->target_district_slug && $article->target_city_slug ? $this->regionalService->findDistrict($article->target_city_slug, $article->target_district_slug) : null,
            'regionalDistricts' => $article->target_city_slug ? $this->regionalService->getDistrictsByCity($article->target_city_slug) : [],
            'provinces' => $this->regionalService->getAllProvinces(),
            'ollamaConfigured' => $ollama->isConfigured(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article, HtmlSanitizerService $sanitizer, WhatsAppCtaService $whatsAppCtas): RedirectResponse
    {
        $article->update($this->validated($request, $sanitizer, $whatsAppCtas, $article));
        $article->tags()->sync($request->input('tags', []));

        return redirect()->route('admin.articles.edit', $article)->with('success', 'Article updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Article deleted.');
    }

    public function aiGenerate(Request $request, OllamaService $ollama): JsonResponse
    {
        if (! $ollama->isConfigured()) {
            return response()->json(['success' => false, 'message' => 'Generator belum dikonfigurasi. Isi OLLAMA_BASE_URL dan OLLAMA_MODEL di environment aplikasi.'], 503);
        }

        $validated = $request->validate([
            'topic' => ['nullable', 'string', 'max:255'],
            'word_count' => ['required', 'integer', 'min:600', 'max:10000'],
            'service_slug' => ['nullable', 'string', Rule::in(array_keys(config('ryo.services', [])))],
            'target_region_scope' => ['nullable', Rule::in(['all', 'province', 'city', 'district'])],
            'target_province_slug' => ['nullable', 'string', 'max:255'],
            'target_city_slug' => ['nullable', 'string'],
            'target_district_slug' => ['nullable', 'string', 'max:255'],
        ]);

        $targetRegion = $this->resolveRegionalTarget($validated);

        $service = filled($validated['service_slug'] ?? null)
            ? config('ryo.services.'.$validated['service_slug'])
            : null;

        $result = $ollama->generateArticle([
            'topic' => $validated['topic'] ?? null,
            'word_count' => (int) $validated['word_count'],
            'service_name' => $service['title'] ?? null,
            'service_slug' => $validated['service_slug'] ?? null,
            'region_name' => $targetRegion['name'],
        ]);

        return response()->json($result, $result['success'] ? 200 : 502);
    }

    public function generateImagePrompt(Request $request, OllamaService $ollama): JsonResponse
    {
        if (! $ollama->isConfigured()) {
            return response()->json(['success' => false, 'message' => 'Generator belum dikonfigurasi. Isi OLLAMA_BASE_URL dan OLLAMA_MODEL di environment aplikasi.'], 503);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'service_slug' => ['nullable', 'string', Rule::in(array_keys(config('ryo.services', [])))],
            'target_region_scope' => ['nullable', Rule::in(['all', 'province', 'city', 'district'])],
            'target_province_slug' => ['nullable', 'string', 'max:255'],
            'target_city_slug' => ['nullable', 'string', 'max:255'],
            'target_district_slug' => ['nullable', 'string', 'max:255'],
        ]);

        $targetRegion = $this->resolveRegionalTarget($validated);

        $service = filled($validated['service_slug'] ?? null)
            ? config('ryo.services.'.$validated['service_slug'])
            : null;

        $result = $ollama->generateImagePrompt([
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'] ?? null,
            'service_name' => $service['title'] ?? null,
            'region_name' => $targetRegion['name'],
        ]);

        return response()->json($result, $result['success'] ? 200 : 502);
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'files' => ['nullable', 'array', 'max:5'],
            'files.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $files = array_values($request->file('files', []));
        $singleFile = $request->file('file') ?? $request->file('image');
        if ($singleFile !== null) {
            $files[] = $singleFile;
        }

        if ($files === []) {
            return response()->json(['success' => false, 'message' => 'Pilih gambar JPG, PNG, atau WebP terlebih dahulu.'], 422);
        }
        if (count($files) > 5) {
            return response()->json(['success' => false, 'message' => 'Unggah maksimal lima gambar sekaligus.'], 422);
        }

        $urls = collect($files)->map(function (UploadedFile $file): string {
            return $this->uploads->url($this->uploads->store($file, 'articles'));
        })->all();

        return response()->json([
            'success' => true,
            'url' => $urls[0],
            'files' => $urls,
            'data' => ['files' => $urls, 'baseurl' => '', 'isImages' => array_fill(0, count($urls), true)],
        ]);
    }

    public function regionalCities(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
        ]);

        return response()->json(['cities' => $this->regionalService->searchCities($validated['q'] ?? '', $validated['province'] ?? null)]);
    }

    public function regionalDistricts(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'city' => ['required', 'string', 'max:255'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        if ($this->regionalService->findCity($validated['city']) === null) {
            return response()->json(['message' => 'Pilih kota atau kabupaten yang tersedia dari data BPS.'], 422);
        }

        return response()->json(['districts' => $this->regionalService->searchDistricts($validated['city'], $validated['q'] ?? '')]);
    }

    private function validated(Request $request, HtmlSanitizerService $sanitizer, WhatsAppCtaService $whatsAppCtas, ?Article $article = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('articles', 'slug')->ignore($article)],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string', 'max:2000000'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'status' => ['required', Rule::in(['draft', 'published', 'scheduled'])],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'focus_keyword' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_image' => ['nullable', 'url', 'max:255'],
            'service_slug' => ['nullable', 'string', Rule::in(array_keys(config('ryo.services', [])))],
            'target_region_scope' => ['nullable', Rule::in(['all', 'province', 'city', 'district'])],
            'target_province_slug' => ['nullable', 'string', 'max:255'],
            'target_city_slug' => ['nullable', 'string', 'max:255'],
            'target_district_slug' => ['nullable', 'string', 'max:255'],
            'image_prompt' => ['nullable', 'string', 'max:3000'],
            'is_ai_generated' => ['nullable', 'boolean'],
        ]);

        $regionalTarget = $this->resolveRegionalTarget($data);
        unset($regionalTarget['name']);
        $data = array_merge($data, $regionalTarget);

        $data['slug'] = filled($data['slug'] ?? null) ? Str::slug($data['slug']) : Str::slug($data['title']);
        $data['content'] = $sanitizer->sanitize($data['content']);
        $data['is_ai_generated'] = (bool) ($data['is_ai_generated'] ?? false);
        if ($data['is_ai_generated']) {
            $data['content'] = $whatsAppCtas->insertFive($data['content'], $data['title']);
        }

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->uploads->store($request->file('featured_image'), 'articles');
        }
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }
        if ($data['status'] === 'scheduled' && empty($data['published_at'])) {
            $request->validate(['published_at' => ['required', 'date', 'after:now']]);
        }
        $data['author_id'] = $request->user()->id;

        return collect($data)->except('tags')->all();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{target_region_scope: string, target_province_slug: ?string, target_city_slug: ?string, target_district_slug: ?string, name: ?string}
     */
    private function resolveRegionalTarget(array $data): array
    {
        $scope = $data['target_region_scope'] ?? null;
        $scope ??= filled($data['target_district_slug'] ?? null) ? 'district' : (filled($data['target_city_slug'] ?? null) ? 'city' : (filled($data['target_province_slug'] ?? null) ? 'province' : 'all'));

        if ($scope === 'all') {
            return ['target_region_scope' => 'all', 'target_province_slug' => null, 'target_city_slug' => null, 'target_district_slug' => null, 'name' => null];
        }

        $city = filled($data['target_city_slug'] ?? null) ? $this->regionalService->findCity((string) $data['target_city_slug']) : null;
        $provinceSlug = filled($data['target_province_slug'] ?? null) ? (string) $data['target_province_slug'] : ($city['province_slug'] ?? null);
        $province = $provinceSlug ? $this->regionalService->findProvince($provinceSlug) : null;

        if ($province === null) {
            throw ValidationException::withMessages(['target_province_slug' => 'Pilih provinsi yang tersedia dari data BPS.']);
        }

        if ($scope === 'province') {
            return ['target_region_scope' => $scope, 'target_province_slug' => $province['slug'], 'target_city_slug' => null, 'target_district_slug' => null, 'name' => 'Provinsi '.$province['name']];
        }

        if ($city === null || $city['province_slug'] !== $province['slug']) {
            throw ValidationException::withMessages(['target_city_slug' => 'Pilih kota atau kabupaten yang berada di provinsi tersebut.']);
        }

        if ($scope === 'city') {
            return ['target_region_scope' => $scope, 'target_province_slug' => $province['slug'], 'target_city_slug' => $city['slug'], 'target_district_slug' => null, 'name' => $city['name']];
        }

        $districtSlug = (string) ($data['target_district_slug'] ?? '');
        $district = $districtSlug !== '' ? $this->regionalService->findDistrict($city['slug'], $districtSlug) : null;
        if ($scope !== 'district' || $district === null) {
            throw ValidationException::withMessages(['target_district_slug' => 'Pilih kecamatan yang tersedia di kota atau kabupaten tersebut.']);
        }

        return [
            'target_region_scope' => $scope,
            'target_province_slug' => $province['slug'],
            'target_city_slug' => $city['slug'],
            'target_district_slug' => $district['slug'],
            'name' => 'Kecamatan '.$district['name'].', '.$city['name'],
        ];
    }

    private function regionalTargetLabel(Article $article): ?string
    {
        return match ($article->target_region_scope) {
            'all' => 'Semua wilayah',
            'province' => $this->regionalService->findProvince($article->target_province_slug ?? '')['name'] ?? 'Provinsi',
            'district' => $this->regionalService->findDistrict($article->target_city_slug ?? '', $article->target_district_slug ?? '')['name'] ?? 'Kecamatan',
            'city' => $this->regionalService->findCity($article->target_city_slug ?? '')['name'] ?? 'Kota / kabupaten',
            default => $article->target_city_slug ? ($this->regionalService->findCity($article->target_city_slug)['name'] ?? null) : null,
        };
    }
}
