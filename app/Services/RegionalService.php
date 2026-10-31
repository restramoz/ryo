<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Provides the city and province selector data from the supplied BPS master file.
 */
class RegionalService
{
    /** @var array<string, array<string, string>>|null */
    private ?array $cities = null;

    /** @var array<string, array<string, string>>|null */
    private ?array $provinces = null;

    /** @var array<string, array<int, array<string, string>>>|null */
    private ?array $districtsByCity = null;

    /** @return array<int, array<string, string>> */
    public function getAllCities(): array
    {
        $this->initialize();

        return array_values($this->cities ?? []);
    }

    /** @return array<int, array<string, string>> */
    public function getAllProvinces(): array
    {
        $this->initialize();

        return array_values($this->provinces ?? []);
    }

    /** @return array<string, string>|null */
    public function findProvince(string $slug): ?array
    {
        $this->initialize();

        foreach ($this->provinces ?? [] as $province) {
            if ($province['slug'] === $slug) {
                return $province;
            }
        }

        return null;
    }

    /** @return array<int, array<string, string>> */
    public function getCitiesByProvince(string $provinceSlug): array
    {
        $this->initialize();

        return array_values(array_filter($this->cities ?? [], static fn (array $city): bool => $city['province_slug'] === $provinceSlug));
    }

    /** @return array<int, array<string, string>> */
    public function getDistrictsByCity(string $citySlug): array
    {
        $this->initialize();

        return $this->districtsByCity[$citySlug] ?? [];
    }

    /** @return array<int, array<string, string>> */
    public function searchDistricts(string $citySlug, string $query = ''): array
    {
        $query = mb_strtolower(trim($query));

        return array_values(array_filter($this->getDistrictsByCity($citySlug), static fn (array $district): bool => $query === '' || str_contains(mb_strtolower($district['name']), $query)));
    }

    /** @return array<string, string>|null */
    public function findDistrict(string $citySlug, string $districtSlug): ?array
    {
        foreach ($this->getDistrictsByCity($citySlug) as $district) {
            if ($district['slug'] === $districtSlug) {
                return $district;
            }
        }

        return null;
    }

    /** @return array<int, array<string, string>> */
    public function searchCities(string $query = '', ?string $provinceSlug = null, int $limit = 60): array
    {
        $this->initialize();
        $query = mb_strtolower(trim($query));

        return array_values(array_slice(array_filter($this->cities ?? [], static function (array $city) use ($query, $provinceSlug): bool {
            return ($provinceSlug === null || $city['province_slug'] === $provinceSlug)
                && ($query === '' || str_contains(mb_strtolower($city['name']), $query));
        }), 0, min(max($limit, 1), 100)));
    }

    /** @return array<string, string>|null */
    public function findCity(string $slug): ?array
    {
        $this->initialize();

        foreach ($this->cities ?? [] as $city) {
            if ($city['slug'] === $slug) {
                return $city;
            }
        }

        return null;
    }

    private function initialize(): void
    {
        if ($this->cities !== null) {
            return;
        }

        $sourceFile = resource_path('data/master_wilayah_bps.json');
        $records = is_file($sourceFile)
            ? json_decode((string) file_get_contents($sourceFile), true)
            : null;

        $cities = [];
        $provinces = [];
        $districts = [];
        $districtSlugs = [];

        foreach (is_array($records) ? $records : [] as $record) {
            $cityCode = trim((string) ($record['Kode Kab/Kota'] ?? ''));
            $provinceCode = trim((string) ($record['Kode Provinsi'] ?? ''));
            $rawName = trim((string) ($record['Kabupaten/Kota'] ?? ''));
            $provinceName = trim((string) ($record['Provinsi'] ?? ''));
            $type = trim((string) ($record['Jenis Wilayah'] ?? ''));

            if ($cityCode === '' || $provinceCode === '' || $rawName === '' || $provinceName === '') {
                continue;
            }

            $slug = $type === 'Kota' ? Str::slug($rawName) : 'kab-'.Str::slug($rawName);
            if (! isset($cities[$cityCode])) {
                $cities[$cityCode] = [
                    'code' => $cityCode,
                    'name' => ($type === 'Kota' ? 'Kota ' : 'Kabupaten ').$rawName,
                    'raw_name' => $rawName,
                    'type' => $type,
                    'slug' => $slug,
                    'province' => $provinceName,
                    'province_code' => $provinceCode,
                    'province_slug' => Str::slug($provinceName),
                ];
            }

            $provinces[$provinceCode] = [
                'code' => $provinceCode,
                'name' => $provinceName,
                'slug' => Str::slug($provinceName),
            ];

            $districtCode = trim((string) ($record['Kode Kecamatan'] ?? ''));
            $districtName = trim((string) ($record['Kecamatan'] ?? ''));
            if ($districtCode !== '' && $districtName !== '' && ! isset($districts[$slug][$districtCode])) {
                $districtSlug = Str::slug($districtName);
                if (isset($districtSlugs[$slug][$districtSlug])) {
                    $districtSlug .= '-'.Str::slug($districtCode);
                }
                $districtSlugs[$slug][Str::slug($districtName)] = true;
                $districts[$slug][$districtCode] = [
                    'code' => $districtCode,
                    'name' => $districtName,
                    'slug' => $districtSlug,
                    'city_slug' => $slug,
                    'city_name' => ($type === 'Kota' ? 'Kota ' : 'Kabupaten ').$rawName,
                    'province' => $provinceName,
                    'province_slug' => Str::slug($provinceName),
                ];
            }
        }

        uasort($cities, static fn (array $first, array $second): int => [$first['province'], $first['name']] <=> [$second['province'], $second['name']]);
        uasort($provinces, static fn (array $first, array $second): int => $first['name'] <=> $second['name']);

        foreach ($districts as &$cityDistricts) {
            uasort($cityDistricts, static fn (array $first, array $second): int => $first['name'] <=> $second['name']);
        }
        unset($cityDistricts);

        $this->cities = $cities;
        $this->provinces = $provinces;
        $this->districtsByCity = $districts;
    }
}
