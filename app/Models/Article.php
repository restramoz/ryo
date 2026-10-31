<?php

namespace App\Models;

use App\Services\HtmlSanitizerService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    protected $fillable = ['title', 'slug', 'excerpt', 'content', 'featured_image', 'image_alt', 'category_id', 'author_id', 'status', 'published_at', 'meta_title', 'meta_description', 'focus_keyword', 'canonical_url', 'og_image', 'service_slug', 'target_region_scope', 'target_province_slug', 'target_city_slug', 'target_district_slug', 'image_prompt', 'is_ai_generated'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'is_ai_generated' => 'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeVisibleInRegion(Builder $query, string $provinceSlug, ?string $citySlug = null, ?string $districtSlug = null): Builder
    {
        return $query->where(function (Builder $regional) use ($provinceSlug, $citySlug, $districtSlug): void {
            $regional->where('target_region_scope', 'all')
                ->orWhere(function (Builder $province) use ($provinceSlug): void {
                    $province->where('target_region_scope', 'province')->where('target_province_slug', $provinceSlug);
                });

            if ($citySlug !== null) {
                $regional->orWhere(function (Builder $city) use ($citySlug): void {
                    $city->where('target_region_scope', 'city')->where('target_city_slug', $citySlug);
                })->orWhere(function (Builder $legacy) use ($citySlug): void {
                    $legacy->whereNull('target_region_scope')->where('target_city_slug', $citySlug);
                });
            }

            if ($districtSlug !== null && $citySlug !== null) {
                $regional->orWhere(function (Builder $district) use ($citySlug, $districtSlug): void {
                    $district->where('target_region_scope', 'district')
                        ->where('target_city_slug', $citySlug)
                        ->where('target_district_slug', $districtSlug);
                });
            }
        });
    }

    public function safeContentHtml(): string
    {
        $document = json_decode($this->content ?? '', true);
        if (is_array($document)) {
            return collect($document['content'] ?? [])->map(fn (array $node): string => $this->renderNode($node))->implode('');
        }

        return app(HtmlSanitizerService::class)->sanitize($this->content ?? '');
    }

    private function renderNode(array $node): string
    {
        $children = collect($node['content'] ?? [])->map(fn (array $child): string => $this->renderNode($child))->implode('');
        $content = e($node['text'] ?? '') ?: $children;
        foreach ($node['marks'] ?? [] as $mark) {
            $tag = match ($mark['type'] ?? '') {
                'bold' => 'strong', 'italic' => 'em', 'strike' => 's', 'code' => 'code', default => null
            };
            if ($tag !== null) {
                $content = "<{$tag}>{$content}</{$tag}>";
            }
        }

        $tag = match ($node['type'] ?? '') {
            'heading' => 'h'.min(6, max(2, (int) ($node['attrs']['level'] ?? 2))), 'bulletList' => 'ul', 'orderedList' => 'ol', 'listItem' => 'li', 'blockquote' => 'blockquote', 'codeBlock' => 'pre', 'horizontalRule' => 'hr', default => 'p'
        };

        return $tag === 'hr' ? '<hr>' : "<{$tag}>{$content}</{$tag}>";
    }
}
