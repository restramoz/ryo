<?php

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\RegionalLandingController;
use App\Models\Article;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Tag;
use App\Services\PublicUploadService;
use App\Services\RegionalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'home'])->name('home');
Route::get('/about', [PublicPageController::class, 'page'])->defaults('page', 'about')->name('about');
Route::get('/services', [PublicPageController::class, 'page'])->defaults('page', 'services')->name('services');
Route::get('/services/{slug}', [PublicPageController::class, 'service'])->name('services.show');
Route::get('/api/regional-cities', [RegionalLandingController::class, 'cities'])->middleware('throttle:60,1')->name('regional.cities');
Route::get('/wilayah', [RegionalLandingController::class, 'index'])->name('regions.index');
Route::get('/wilayah/provinsi/{provinceSlug}', [RegionalLandingController::class, 'province'])->name('regions.province');
Route::get('/wilayah/{citySlug}/kecamatan/{districtSlug}', [RegionalLandingController::class, 'district'])->name('regions.district');
Route::get('/wilayah/{citySlug}/layanan/{serviceSlug}', RegionalLandingController::class)->name('regional.show');
Route::get('/wilayah/{citySlug}', [RegionalLandingController::class, 'city'])->name('regions.city');
Route::get('/pricing', [PublicPageController::class, 'page'])->defaults('page', 'pricing')->name('pricing');
Route::get('/portfolio', [PublicPageController::class, 'portfolio'])->name('portfolio');
Route::get('/portfolio/{slug}', [PublicPageController::class, 'project'])->name('portfolio.show');
Route::get('/blog', [PublicPageController::class, 'blog'])->name('blog');
Route::get('/blog/{article:slug}', [PublicPageController::class, 'article'])->name('blog.show');
Route::get('/contact', [PublicPageController::class, 'page'])->defaults('page', 'contact')->name('contact');

Route::get('/sitemap.xml', function (RegionalService $regionalService) {
    $cities = $regionalService->getAllCities();
    $citiesByProvince = [];
    $citiesBySlug = [];
    foreach ($cities as $city) {
        $citiesByProvince[$city['province_slug']][] = $city;
        $citiesBySlug[$city['slug']] = $city;
    }

    $regionalPages = [];
    $regionalArticles = Article::published()->whereNotNull('service_slug')->get([
        'service_slug',
        'target_region_scope',
        'target_province_slug',
        'target_city_slug',
    ]);
    foreach ($regionalArticles as $article) {
        $targetCities = match ($article->target_region_scope) {
            'all' => $cities,
            'province' => $citiesByProvince[$article->target_province_slug] ?? [],
            'city' => isset($citiesBySlug[$article->target_city_slug]) ? [$citiesBySlug[$article->target_city_slug]] : [],
            default => $article->target_region_scope === null && isset($citiesBySlug[$article->target_city_slug]) ? [$citiesBySlug[$article->target_city_slug]] : [],
        };

        foreach ($targetCities as $city) {
            $key = $city['slug'].'|'.$article->service_slug;
            $regionalPages[$key] = (object) ['target_city_slug' => $city['slug'], 'service_slug' => $article->service_slug];
        }
    }

    $districts = [];
    foreach ($cities as $city) {
        array_push($districts, ...$regionalService->getDistrictsByCity($city['slug']));
    }

    return response()->view('sitemap', [
        'articles' => Article::published()->get(['slug', 'updated_at']),
        'regionalPages' => array_values($regionalPages),
        'provinces' => $regionalService->getAllProvinces(),
        'cities' => $cities,
        'districts' => $districts,
        'categories' => Category::query()->get(['slug', 'updated_at']),
        'tags' => Tag::query()->get(['slug', 'updated_at']),
    ])->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'create'])->name('login');
        Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:6,1')->name('login');
    });
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
        Route::get('/', function () {
            return view('admin.dashboard', ['counts' => ['articles' => Article::count(), 'published' => Article::where('status', 'published')->count(), 'drafts' => Article::where('status', 'draft')->count()]]);
        })->name('dashboard');
        Route::post('articles/ai-generate', [AdminArticleController::class, 'aiGenerate'])->middleware('throttle:5,1')->name('articles.ai-generate');
        Route::post('articles/image-prompt', [AdminArticleController::class, 'generateImagePrompt'])->middleware('throttle:10,1')->name('articles.image-prompt');
        Route::post('articles/upload-image', [AdminArticleController::class, 'uploadImage'])->middleware('throttle:20,1')->name('articles.upload-image');
        Route::get('regional-cities', [AdminArticleController::class, 'regionalCities'])->name('regional-cities');
        Route::get('regional-districts', [AdminArticleController::class, 'regionalDistricts'])->name('regional-districts');
        Route::resource('articles', AdminArticleController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('tags', TagController::class);
        Route::get('/media', function (PublicUploadService $uploads) {
            return view('admin.media', ['files' => $uploads->files('articles')]);
        })->name('media');
        Route::get('/settings', fn () => view('admin.settings'))->name('settings');
        Route::put('/settings', function (Request $request) {
            $data = $request->validate(['email' => ['nullable', 'email'], 'location' => ['nullable', 'string', 'max:255'], 'instagram' => ['nullable', 'url'], 'linkedin' => ['nullable', 'url'], 'behance' => ['nullable', 'url']]);
            foreach ($data as $key => $value) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }

            return back()->with('success', 'Website settings updated.');
        })->name('settings.update');
    });
});
