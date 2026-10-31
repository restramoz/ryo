<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OllamaService
{
    public function __construct(
        private readonly HtmlSanitizerService $sanitizer,
        private readonly WhatsAppCtaService $whatsAppCtas,
    ) {}

    public function isConfigured(): bool
    {
        return filled(config('services.ollama.base_url')) && filled(config('services.ollama.model'));
    }

    /**
     * @param  array{topic?: string|null, service_slug?: string|null, service_name?: string|null, city_name?: string|null, region_name?: string|null, word_count?: int}  $params
     * @return array<string, mixed>
     */
    public function generateArticle(array $params): array
    {
        $serviceName = (string) ($params['service_name'] ?? '');
        $regionName = (string) ($params['region_name'] ?? $params['city_name'] ?? '');
        $regionName = $regionName !== '' ? $regionName : 'Seluruh Indonesia';
        $topic = trim((string) ($params['topic'] ?? ''));
        $wordCount = (int) ($params['word_count'] ?? 1200);
        $location = $regionName !== '' ? " untuk {$regionName}" : ' untuk bisnis di Indonesia';
        $serviceContext = $serviceName !== '' ? "Layanan terkait: {$serviceName}." : 'Pilih konteks yang relevan dengan layanan digital Ryo Digital Website.';
        $topicContext = $topic !== '' ? $topic : "Strategi {$serviceName} yang efektif{$location}";

        $targetContentWords = max(1, $wordCount - $this->whatsAppCtas->wordCount());
        $initialChunkWords = min(900, $targetContentWords);

        $systemPrompt = <<<'PROMPT'
Anda adalah editor SEO dan konsultan digital berbahasa Indonesia untuk Ryo Digital Website, studio pengembangan website yang berfokus pada bisnis Indonesia.
Tulis artikel yang bermanfaat, akurat, spesifik, dan mudah dibaca. Jangan mengarang klien, studi kasus, statistik, harga, alamat, jaminan peringkat Google, atau fakta lokal. Gunakan lokasi hanya sebagai konteks kebutuhan audiens; jangan membuat klaim tentang kota yang tidak diberikan. Hindari pengulangan dan frasa klise AI.
Jangan tulis nomor telepon, tautan WhatsApp, atau blok ajakan konsultasi dalam properti content. Sistem akan menyisipkan lima blok konsultasi secara merata di artikel.
Kembalikan hanya JSON valid dengan properti: title, slug, excerpt, content, meta_title, meta_description, focus_keyword, image_prompt, image_alt.
Properti content harus berupa HTML semantik berbahasa Indonesia dengan tag h2, h3, p, ul, ol, li, strong, blockquote, table jika diperlukan. Jangan sertakan script, style, iframe, markdown fence, atau komentar.
Properti image_prompt berisi prompt bahasa Inggris untuk foto editorial realistis yang relevan dengan artikel, tanpa teks, watermark, logo, atau klaim visual palsu. Properti image_alt berisi teks alternatif singkat dalam Bahasa Indonesia.
PROMPT;

        $userPrompt = <<<PROMPT
Buat artikel SEO dengan arahan berikut:
- Topik: {$topicContext}
- Layanan Ryo Digital Website: {$serviceName}
- Konteks layanan: {$serviceContext}
- Wilayah target: {$regionName}
- Panjang artikel akhir yang diminta: {$wordCount} kata. Sistem menyisipkan lima CTA WhatsApp, jadi konten artikel harus mencapai sekitar {$targetContentWords} kata.
- Tulis bagian pembuka pertama sekitar {$initialChunkWords} kata. Artikel akan dilanjutkan otomatis dalam beberapa bagian; jangan mencoba memaksakan seluruh artikel dalam satu respons.
- Audiens: pemilik UMKM, perusahaan, lembaga pendidikan, penyedia jasa profesional, dan personal brand di Indonesia.
- Tujuan: memberi panduan yang berguna dan menghubungkan topik secara natural ke layanan digital yang relevan.
- Akhiri artikel dengan ajakan berdiskusi bersama Ryo Digital Website tanpa menjanjikan hasil tertentu.

Kembalikan JSON saja.
PROMPT;

        $response = $this->chat([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ], true, min(5000, max(2048, $initialChunkWords * 3)));

        if (! $response['success']) {
            return $response;
        }

        $article = $this->decodeJson((string) $response['content']);

        if (! is_array($article) || blank($article['title'] ?? null) || blank($article['content'] ?? null)) {
            return ['success' => false, 'message' => 'Ollama merespons, tetapi hasil artikel tidak memiliki judul atau konten yang valid. Coba ulangi dengan topik yang lebih spesifik.'];
        }

        $title = Str::limit(trim(strip_tags((string) $article['title']), " \t\n\r\0\x0B\"'"), 255, '');
        $excerpt = Str::limit(trim(strip_tags((string) ($article['excerpt'] ?? ''))), 1000, '');
        $content = $this->sanitizer->sanitize((string) $article['content']);

        if ($content === '') {
            return ['success' => false, 'message' => 'Konten dari Ollama kosong setelah pemeriksaan keamanan. Coba buat ulang artikel.'];
        }

        $continuation = $this->completeArticle($content, $targetContentWords, [
            'topic' => $topicContext,
            'service' => $serviceName,
            'location' => $regionName,
            'title' => $title,
        ]);

        if (! $continuation['success']) {
            return $continuation;
        }

        $content = $this->whatsAppCtas->insertFive($continuation['content'], $title);
        $finalWordCount = $this->countWords($content);
        if ($finalWordCount < $wordCount) {
            return ['success' => false, 'message' => "Artikel belum mencapai target {$wordCount} kata (saat ini {$finalWordCount} kata). Coba lagi atau naikkan OLLAMA_TIMEOUT di .env."];
        }

        return [
            'success' => true,
            'title' => $title,
            'slug' => Str::slug((string) ($article['slug'] ?? $title)),
            'excerpt' => $excerpt !== '' ? $excerpt : Str::limit(strip_tags($content), 190),
            'content' => $content,
            'meta_title' => Str::limit(trim(strip_tags((string) ($article['meta_title'] ?? $title))), 255, ''),
            'meta_description' => Str::limit(trim(strip_tags((string) ($article['meta_description'] ?? $excerpt))), 500, ''),
            'focus_keyword' => Str::limit(trim(strip_tags((string) ($article['focus_keyword'] ?? $serviceName))), 255, ''),
            'image_prompt' => Str::limit(trim(strip_tags((string) ($article['image_prompt'] ?? ''))), 3000, ''),
            'image_alt' => Str::limit(trim(strip_tags((string) ($article['image_alt'] ?? $title))), 255, ''),
            'is_ai_generated' => true,
        ];
    }

    /**
     * @param  array{title?: string|null, excerpt?: string|null, service_name?: string|null, city_name?: string|null, region_name?: string|null}  $params
     * @return array{success: bool, prompt?: string, message?: string}
     */
    public function generateImagePrompt(array $params): array
    {
        $title = trim((string) ($params['title'] ?? ''));
        $excerpt = trim((string) ($params['excerpt'] ?? ''));

        if ($title === '') {
            return ['success' => false, 'message' => 'Isi judul artikel sebelum membuat prompt gambar.'];
        }

        $context = implode("\n", array_filter([
            'Judul: '.$title,
            $excerpt !== '' ? 'Ringkasan: '.$excerpt : null,
            filled($params['service_name'] ?? null) ? 'Layanan: '.$params['service_name'] : null,
            filled($params['region_name'] ?? $params['city_name'] ?? null) ? 'Konteks wilayah: '.($params['region_name'] ?? $params['city_name']) : null,
        ]));

        $response = $this->chat([
            ['role' => 'system', 'content' => 'You create concise, specific English image-generation prompts for premium editorial photography. Return only the prompt text. Describe a realistic scene relevant to the supplied article. No text, typography, logo, watermark, UI mockup, or fabricated statistics in the image. Use natural light, believable Indonesian context, restrained colors, and a wide 16:9 composition.'],
            ['role' => 'user', 'content' => "Create one image prompt for this article:\n{$context}"],
        ], false, 500);

        if (! $response['success']) {
            return ['success' => false, 'message' => $response['message']];
        }

        $prompt = trim((string) $response['content'], " \t\n\r\0\x0B\"'` ");

        return $prompt !== ''
            ? ['success' => true, 'prompt' => Str::limit($prompt, 3000, '')]
            : ['success' => false, 'message' => 'Ollama mengembalikan prompt gambar kosong. Coba lagi.'];
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     * @return array{success: bool, content?: string, message?: string}
     */
    private function chat(array $messages, bool $json = true, int $maxTokens = 2048, int $contextWindow = 0): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'message' => 'Generator belum dikonfigurasi. Isi OLLAMA_BASE_URL dan OLLAMA_MODEL di environment aplikasi.'];
        }

        $baseUrl = rtrim((string) config('services.ollama.base_url'), '/');
        $endpoint = match (true) {
            str_ends_with($baseUrl, '/api/chat') => $baseUrl,
            str_ends_with($baseUrl, '/api') => $baseUrl.'/chat',
            default => $baseUrl.'/api/chat',
        };
        $timeout = max(10, (int) config('services.ollama.timeout', 900));
        if (function_exists('set_time_limit')) {
            @set_time_limit($timeout + 15);
        }
        $request = Http::connectTimeout(min(10, $timeout))->timeout($timeout)->acceptJson();
        $apiKey = config('services.ollama.key');

        if (filled($apiKey)) {
            $request = $request->withToken((string) $apiKey);
        }

        try {
            $payload = [
                'model' => config('services.ollama.model'),
                'messages' => $messages,
                'stream' => false,
                'options' => [
                    'num_predict' => min(24000, max(256, $maxTokens)),
                    'num_ctx' => min(32768, max(4096, $contextWindow ?: $maxTokens + 2048)),
                ],
            ];
            if ($json) {
                $payload['format'] = 'json';
            }
            $response = $request->post($endpoint, $payload);

            if (! $response->successful()) {
                Log::warning('Ollama request returned a non-success status.', ['status' => $response->status()]);

                return ['success' => false, 'message' => 'Ollama mengembalikan HTTP '.$response->status().'. Periksa endpoint, model, dan koneksi layanan.'];
            }

            $content = $response->json('message.content') ?? $response->json('response');

            return filled($content)
                ? ['success' => true, 'content' => (string) $content]
                : ['success' => false, 'message' => 'Ollama mengembalikan respons kosong. Coba lagi.'];
        } catch (ConnectionException) {
            return ['success' => false, 'message' => 'Tidak dapat terhubung ke Ollama. Pastikan layanan aktif dan endpoint dapat dijangkau server.'];
        } catch (\Throwable $exception) {
            Log::error('Ollama request failed.', ['exception' => $exception::class]);

            return ['success' => false, 'message' => 'Terjadi kesalahan saat berkomunikasi dengan Ollama. Periksa log aplikasi untuk detailnya.'];
        }
    }

    /**
     * Continue a short model response in fresh, non-overlapping sections until the requested length is reached.
     *
     * @param  array{topic: string, service: string, location: string, title: string}  $context
     * @return array{success: bool, content?: string, message?: string}
     */
    private function completeArticle(string $content, int $targetWords, array $context): array
    {
        $systemPrompt = 'Anda adalah editor artikel SEO berbahasa Indonesia. Lanjutkan artikel yang diberikan dengan informasi baru, akurat, terstruktur, bermanfaat, dan konsisten. Jangan mengulang, merangkum, atau memparafrasekan paragraf sebelumnya. Jangan menambahkan judul artikel, penutup/CTA WhatsApp, nomor telepon, atau tautan. Keluarkan hanya HTML semantik lanjutan (h2, h3, p, ul, ol, li, strong, blockquote, table bila relevan), tanpa markdown fence, script, atau komentar. Jangan mengarang data, statistik, klien, studi kasus, maupun fakta lokal.';
        $attempts = 0;
        $stalled = 0;
        $seen = [];
        $lastSection = '';

        while (($remaining = $targetWords - $this->countWords($content)) > 0 && $attempts < 24) {
            $attempts++;
            $chunkTarget = min(800, $remaining);
            $request = $this->chat([
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => "Kembangkan artikel unik berjudul \"{$context['title']}\" tentang {$context['topic']}. Layanan terkait: {$context['service']}. Wilayah: {$context['location']}.\n\nArtikel yang sudah ada (hanya sebagai konteks, jangan tulis ulang):\n{$content}\n\nTambahkan bagian lanjutan sekitar {$chunkTarget} kata. Mulai dari subbagian berikutnya, bahas sudut pandang baru secara mendalam, dan jangan mengulang isi lama. Keluaran harus berupa HTML saja."],
            ], false, min(5000, max(1200, $chunkTarget * 3)), 32768);

            if (! $request['success']) {
                return ['success' => false, 'message' => "Artikel baru mencapai {$this->countWords($content)} dari {$targetWords} kata karena proses lanjutan gagal. {$request['message']} Coba lagi atau periksa OLLAMA_TIMEOUT di .env."];
            }

            $section = $this->sanitizer->sanitize((string) $request['content']);
            $plainSection = trim(html_entity_decode(strip_tags($section), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $signature = hash('sha256', mb_strtolower(preg_replace('/\s+/u', ' ', $plainSection) ?? $plainSection));
            $addedWords = $this->countWords($section);

            if ($addedWords < 25 || isset($seen[$signature]) || ($lastSection !== '' && str_contains(mb_strtolower($lastSection), mb_strtolower(mb_substr($plainSection, 0, 180))))) {
                $stalled++;
                if ($stalled >= 3) {
                    return ['success' => false, 'message' => "Ollama berhenti menambahkan konten baru pada {$this->countWords($content)} dari {$targetWords} kata. Coba model dengan context/output lebih besar atau tingkatkan OLLAMA_TIMEOUT di .env."];
                }

                continue;
            }

            $seen[$signature] = true;
            $lastSection = $plainSection;
            $content .= "\n".$section;
            $stalled = 0;
        }

        $actualWords = $this->countWords($content);
        if ($actualWords < $targetWords) {
            return ['success' => false, 'message' => "Artikel mencapai {$actualWords} dari {$targetWords} kata setelah {$attempts} bagian. Naikkan OLLAMA_TIMEOUT atau gunakan model dengan batas output lebih besar, lalu coba kembali."];
        }

        return ['success' => true, 'content' => $content];
    }

    private function countWords(string $html): int
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $words = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY);

        return count($words ?: []);
    }

    /** @return array<string, mixed>|null */
    private function decodeJson(string $content): ?array
    {
        $cleaned = trim($content);
        $cleaned = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $cleaned) ?? $cleaned;
        $decoded = json_decode(trim($cleaned), true);

        if (is_array($decoded)) {
            return $decoded;
        }

        $start = strpos($cleaned, '{');
        $end = strrpos($cleaned, '}');

        if ($start === false || $end === false || $end <= $start) {
            return null;
        }

        $decoded = json_decode(substr($cleaned, $start, $end - $start + 1), true);

        return is_array($decoded) ? $decoded : null;
    }
}
