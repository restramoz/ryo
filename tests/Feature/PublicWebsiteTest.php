<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PublicWebsiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_public_pages_are_available(): void
    {
        foreach (['/', '/about', '/services', '/services/website-development', '/portfolio', '/portfolio/trainingkota', '/blog', '/wilayah', '/contact', '/sitemap.xml'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_public_brand_and_canonical_urls_use_the_configured_identity(): void
    {
        config(['app.name' => 'Ryo Digital Website', 'app.url' => 'https://ryodigitalmalang.my-id.id']);

        $this->get('/')
            ->assertOk()
            ->assertSee('<title>Ryo Digital Website — Website dan Pengalaman Digital untuk Bisnis</title>', false)
            ->assertSee('<link rel="canonical" href="https://ryodigitalmalang.my-id.id/">', false)
            ->assertSee('"name":"Ryo Digital Website"', false)
            ->assertSee('"url":"https://ryodigitalmalang.my-id.id"', false);
    }

    public function test_published_articles_are_visible_and_drafts_are_private(): void
    {
        $published = Article::create(['title' => 'A published article', 'slug' => 'published-article', 'content' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Useful advice"}]}]}', 'status' => 'published', 'published_at' => now()->subMinute()]);
        $draft = Article::create(['title' => 'A private draft', 'slug' => 'private-draft', 'status' => 'draft']);

        $this->get('/blog')->assertOk()->assertSee($published->title)->assertDontSee($draft->title);
        $this->get('/blog/'.$published->slug)->assertOk()->assertSee('Useful advice');
        $this->get('/blog/'.$draft->slug)->assertNotFound();
    }

    public function test_admin_pages_require_authentication(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();
    }

    public function test_authenticated_editor_can_access_cms_and_create_an_article(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/admin')->assertOk();
        $this->get('/admin/articles')->assertOk();
        $this->get('/admin/articles/create')->assertOk()->assertSee('data-jodit-content', false)->assertSee('data-generate-article', false);
        $this->getJson('/admin/regional-districts?city=malang')->assertOk()->assertJsonFragment(['name' => 'Klojen', 'slug' => 'klojen']);
        $this->get('/admin/categories')->assertOk();
        $this->get('/admin/tags')->assertOk();
        $this->get('/admin/media')->assertOk();
        $this->get('/admin/settings')->assertOk();

        $response = $this->post('/admin/articles', [
            'title' => 'A CMS article',
            'excerpt' => 'An article created from the admin workspace.',
            'content' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"A safe structured document."}]}]}',
            'status' => 'draft',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('articles', ['slug' => 'a-cms-article', 'status' => 'draft']);
        $this->get($response->headers->get('Location'))->assertOk()->assertSee('Hapus artikel?');

        $this->post('/admin/articles', [
            'title' => 'A district scoped article',
            'content' => '<p>Regional guidance.</p>',
            'status' => 'draft',
            'target_region_scope' => 'district',
            'target_province_slug' => 'jawa-timur',
            'target_city_slug' => 'malang',
            'target_district_slug' => 'klojen',
        ])->assertRedirect();

        $this->assertDatabaseHas('articles', [
            'slug' => 'a-district-scoped-article',
            'target_region_scope' => 'district',
            'target_province_slug' => 'jawa-timur',
            'target_city_slug' => 'malang',
            'target_district_slug' => 'klojen',
        ]);
    }

    public function test_regional_articles_follow_all_and_selected_parent_scopes(): void
    {
        $base = ['status' => 'published', 'published_at' => now()->subMinute(), 'content' => '<p>Regional article content.</p>'];
        Article::create($base + ['title' => 'All regions article', 'slug' => 'all-regions-article', 'target_region_scope' => 'all', 'service_slug' => 'website-development']);
        Article::create($base + ['title' => 'East Java province article', 'slug' => 'east-java-province-article', 'target_region_scope' => 'province', 'target_province_slug' => 'jawa-timur']);
        Article::create($base + ['title' => 'Malang city article', 'slug' => 'malang-city-article', 'target_region_scope' => 'city', 'target_province_slug' => 'jawa-timur', 'target_city_slug' => 'malang']);
        Article::create($base + ['title' => 'Klojen district article', 'slug' => 'klojen-district-article', 'target_region_scope' => 'district', 'target_province_slug' => 'jawa-timur', 'target_city_slug' => 'malang', 'target_district_slug' => 'klojen']);
        Article::create($base + ['title' => 'Lowokwaru district article', 'slug' => 'lowokwaru-district-article', 'target_region_scope' => 'district', 'target_province_slug' => 'jawa-timur', 'target_city_slug' => 'malang', 'target_district_slug' => 'lowokwaru']);

        $this->get('/wilayah/provinsi/jawa-timur')->assertOk()->assertSee('All regions article')->assertSee('East Java province article')->assertDontSee('Malang city article');
        $this->get('/wilayah/malang')->assertOk()->assertSee('All regions article')->assertSee('East Java province article')->assertSee('Malang city article')->assertDontSee('Klojen district article');
        $this->get('/wilayah/malang/kecamatan/klojen')->assertOk()->assertSee('All regions article')->assertSee('East Java province article')->assertSee('Malang city article')->assertSee('Klojen district article')->assertDontSee('Lowokwaru district article');
        $this->get('/wilayah/provinsi/jawa-tengah')->assertOk()->assertSee('All regions article')->assertDontSee('East Java province article');
        $this->get('/wilayah/malang/layanan/website-development')->assertOk()->assertSee('All regions article');
    }

    public function test_ollama_can_generate_an_article_draft_and_image_prompt(): void
    {
        $this->actingAs(User::factory()->create());
        config(['services.ollama.base_url' => 'http://ollama.test', 'services.ollama.model' => 'test-model']);
        Http::fake([
            'ollama.test/api/chat' => Http::sequence()
                ->push(['message' => ['content' => json_encode([
                    'title' => 'Panduan Website Bisnis di Malang',
                    'slug' => 'panduan-website-bisnis-malang',
                    'excerpt' => 'Panduan untuk memilih fondasi website.',
                    'content' => '<h2>Mulai dari kebutuhan</h2><p>Pilih website sesuai tujuan bisnis.</p>',
                    'meta_title' => 'Panduan Website Bisnis',
                    'meta_description' => 'Pelajari dasar memilih website bisnis.',
                    'focus_keyword' => 'website bisnis Malang',
                    'image_prompt' => 'Editorial photo of a local Indonesian business owner.',
                    'image_alt' => 'Pemilik bisnis sedang bekerja',
                ])]], 200)
                ->push(['message' => ['content' => '<h2>Langkah praktis berikutnya</h2><p>'.implode(' ', array_map(fn (int $word): string => 'panduan'.str_pad((string) $word, 3, '0', STR_PAD_LEFT), range(1, 560))).'</p>']], 200)
                ->push(['message' => ['content' => 'Editorial photograph of an Indonesian business owner at work, natural light, wide 16:9.']], 200),
        ]);

        $generated = $this->postJson('/admin/articles/ai-generate', [
            'topic' => 'Panduan website bisnis',
            'word_count' => 600,
            'service_slug' => 'website-development',
            'target_region_scope' => 'district',
            'target_province_slug' => 'jawa-timur',
            'target_city_slug' => 'malang',
            'target_district_slug' => 'klojen',
        ])->assertOk()->assertJsonPath('success', true)->assertJsonPath('title', 'Panduan Website Bisnis di Malang')->assertJsonPath('is_ai_generated', true);
        $this->assertSame(5, substr_count($generated->json('content'), '085648983077'));
        $this->assertGreaterThanOrEqual(600, str_word_count(strip_tags($generated->json('content'))));

        $this->postJson('/admin/articles/image-prompt', [
            'title' => 'Panduan Website Bisnis di Malang',
            'service_slug' => 'website-development',
            'target_city_slug' => 'malang',
        ])->assertOk()->assertJsonPath('success', true)->assertJsonPath('prompt', 'Editorial photograph of an Indonesian business owner at work, natural light, wide 16:9.');
    }

    public function test_bps_region_search_and_published_regional_service_page(): void
    {
        $this->getJson('/api/regional-cities?q=Malang')->assertOk()->assertJsonFragment(['name' => 'Kota Malang', 'slug' => 'malang']);
        $this->get('/wilayah/provinsi/jawa-timur')->assertOk()->assertSee('Kota Malang');
        $this->get('/wilayah/malang')->assertOk()->assertSee('Kecamatan Klojen');
        $this->get('/wilayah/malang/kecamatan/klojen')->assertOk()->assertSee('Kecamatan Klojen');

        $this->get('/wilayah/malang/layanan/website-development')->assertNotFound();
        $article = Article::create([
            'title' => 'Website bisnis di Malang',
            'slug' => 'website-bisnis-malang',
            'excerpt' => 'Panduan lokal.',
            'content' => '<p>Tips website untuk bisnis.</p>',
            'service_slug' => 'website-development',
            'target_city_slug' => 'malang',
            'status' => 'published',
            'published_at' => now()->subMinute(),
        ]);

        $this->get('/wilayah/malang/layanan/website-development')->assertOk()->assertSee($article->title)->assertSee('Kota Malang');
        $this->get('/sitemap.xml')->assertOk()->assertSee('/wilayah/malang/layanan/website-development');
    }
}
