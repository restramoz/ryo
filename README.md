# Ryo Digital Website

A Laravel 13 website and compact article CMS for Ryo Digital Website. The public site uses Blade, Tailwind CSS v4, Flowbite, and Alpine.js. The article dashboard uses Jodit and can generate Indonesian SEO article drafts with Ollama.

## Local development

Requirements: PHP 8.3+, Composer, Node.js 20+, and npm.

```sh
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm install
npm run dev
php artisan serve
```

Open `http://127.0.0.1:8000`. Create the first administrator locally with `php artisan tinker`, then run `App\Models\User::create(['name' => 'Site Admin', 'email' => 'you@example.com', 'password' => Illuminate\Support\Facades\Hash::make('a-long-unique-password')]);`. The private dashboard is at `/admin/login`; there is no public registration route.

## Website settings

Contact details and social URLs can be set in the dashboard at `/admin/settings`. Empty details remain hidden. Environment values are also supported through `RYO_EMAIL`, `RYO_LOCATION`, `RYO_INSTAGRAM`, `RYO_LINKEDIN`, and `RYO_BEHANCE` in `.env`. The WhatsApp contact is configured as `085648983077`.

## Content management

The dashboard supports article drafting, scheduling, publishing, category and tag management, featured image uploads, Jodit rich text content, and SEO metadata. Uploaded files use Laravel's public disk; run `php artisan storage:link` once to expose images. Scheduled article dates are stored in UTC according to the configured application timezone.

To enable article and image-prompt generation, configure `OLLAMA_BASE_URL` and `OLLAMA_MODEL` in `.env`. Set `OLLAMA_API_KEY` only when the Ollama-compatible endpoint requires one, and adjust `OLLAMA_TIMEOUT` for slower models or long drafts (default 1,800 seconds; article output is capped at 10,000 words). Long articles are generated in successive sections and checked against the requested word count. For long drafts, set PHP-FPM and reverse-proxy request timeouts above the Ollama timeout as well. For a local Ollama instance, the URL is commonly `http://127.0.0.1:11434`; the selected model must already be available to that Ollama server. After changing environment values on a cached deployment, run `php artisan config:cache` again. The generator saves a draft for review; verify its claims and citations before publishing.

Article regional targeting and the public service-area contact selector use the included `resources/data/master_wilayah_bps.json` BPS administrative-area data. Regional SEO pages appear only when an article for that service and location has been published.

## Production deployment

1. Configure `APP_ENV=production`, `APP_DEBUG=false`, a secure `APP_KEY`, `APP_URL=https://ryodigitalmalang.my-id.id`, and production database credentials. SQLite is the local default; migrations use Laravel's portable schema APIs and can run on MySQL.
2. Run `composer install --no-dev --optimize-autoloader`, `npm ci`, and `npm run build` during deployment.
3. Run `php artisan migrate --force`, `php artisan storage:link`, then `php artisan config:cache` and `php artisan route:cache`.
4. Point the web server document root at `public/`, provide writable `storage/` and `bootstrap/cache/`, and configure HTTPS.

The XML sitemap is available at `/sitemap.xml`. Robots rules are in `public/robots.txt`.
