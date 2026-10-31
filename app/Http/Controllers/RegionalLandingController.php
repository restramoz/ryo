<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\RegionalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegionalLandingController extends Controller
{
    public function index(RegionalService $regionalService): View
    {
        return view('pages.regions.index', [
            'provinces' => $this->provinceSummaries($regionalService),
            'title' => 'Wilayah Layanan Website & Digital · Ryo Digital Website',
            'description' => 'Jelajahi layanan website dan digital Ryo Digital Website berdasarkan provinsi, kabupaten, kota, dan kecamatan di Indonesia.',
        ]);
    }

    public function province(string $provinceSlug, RegionalService $regionalService): View
    {
        $province = $regionalService->findProvince($provinceSlug);
        abort_unless($province, 404);
        $cities = $regionalService->getCitiesByProvince($provinceSlug);
        $districtCounts = [];
        foreach ($cities as $city) {
            $districtCounts[$city['slug']] = count($regionalService->getDistrictsByCity($city['slug']));
        }

        return view('pages.regions.province', [
            'province' => $province,
            'cities' => $cities,
            'districtCount' => array_sum($districtCounts),
            'districtCounts' => $districtCounts,
            'services' => config('ryo.services'),
            'regionalArticles' => Article::published()->visibleInRegion($provinceSlug)->latest('published_at')->limit(6)->get(),
            'title' => 'Layanan Website & Digital di '.$province['name'].' · Ryo Digital Website',
            'description' => 'Layanan pengembangan website, e-commerce, aplikasi web, desain UI/UX, SEO, dan otomasi untuk UMKM serta bisnis di '.$province['name'].'.',
        ]);
    }

    public function city(string $citySlug, RegionalService $regionalService): View
    {
        $city = $regionalService->findCity($citySlug);
        abort_unless($city, 404);

        return view('pages.regions.city', [
            'city' => $city,
            'districts' => $regionalService->getDistrictsByCity($citySlug),
            'services' => config('ryo.services'),
            'regionalArticles' => Article::published()->visibleInRegion($city['province_slug'], $citySlug)->latest('published_at')->limit(6)->get(),
            'title' => 'Layanan Website & Digital di '.$city['name'].' · Ryo Digital Website',
            'description' => 'Ryo Digital Website membantu UMKM, perusahaan, lembaga pendidikan, dan penyedia jasa di '.$city['name'].' membangun website dan solusi digital yang relevan.',
        ]);
    }

    public function district(string $citySlug, string $districtSlug, RegionalService $regionalService): View
    {
        $city = $regionalService->findCity($citySlug);
        $district = $regionalService->findDistrict($citySlug, $districtSlug);
        abort_unless($city && $district, 404);

        return view('pages.regions.district', [
            'city' => $city,
            'district' => $district,
            'services' => config('ryo.services'),
            'regionalArticles' => Article::published()->visibleInRegion($city['province_slug'], $citySlug, $districtSlug)->latest('published_at')->limit(6)->get(),
            'title' => 'Website & Layanan Digital di Kecamatan '.$district['name'].' · Ryo Digital Website',
            'description' => 'Pentingnya website untuk UMKM dan bisnis di Kecamatan '.$district['name'].', '.$city['name'].'. Jelajahi layanan website Ryo Digital Website.',
        ]);
    }

    public function cities(Request $request, RegionalService $regionalService): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
        ]);

        return response()->json(['cities' => $regionalService->searchCities($validated['q'] ?? '', $validated['province'] ?? null)]);
    }

    public function __invoke(string $citySlug, string $serviceSlug, RegionalService $regionalService): View
    {
        $city = $regionalService->findCity($citySlug);
        $service = config('ryo.services.'.$serviceSlug);

        abort_unless($city && $service, 404);

        $articles = Article::published()
            ->with(['category', 'tags'])
            ->visibleInRegion($city['province_slug'], $citySlug)
            ->where('service_slug', $serviceSlug)
            ->latest('published_at')
            ->paginate(9);

        abort_if($articles->total() === 0, 404);
        abort_if($articles->isEmpty() && $articles->currentPage() > 1, 404);

        return view('pages.regional', [
            'city' => $city,
            'service' => $service,
            'serviceSlug' => $serviceSlug,
            'articles' => $articles,
            'title' => $service['title'].' di '.$city['name'].' · Ryo Digital Website',
            'description' => $service['title'].' untuk bisnis di '.$city['name'].'. Pelajari panduan dan insight digital dari Ryo Digital Website.',
        ]);
    }

    /** @return array<int, array<string, mixed>> */
    private function provinceSummaries(RegionalService $regionalService): array
    {
        return array_map(function (array $province) use ($regionalService): array {
            $cities = $regionalService->getCitiesByProvince($province['slug']);

            return [
                ...$province,
                'city_count' => count($cities),
                'district_count' => array_sum(array_map(fn (array $city): int => count($regionalService->getDistrictsByCity($city['slug'])), $cities)),
            ];
        }, $regionalService->getAllProvinces());
    }
}
