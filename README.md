# JobYaari Blogs

A production-grade blog management system for government job notifications — built end-to-end in Laravel 11 for the JobYaari developer internship assignment.

> Public site for readers + a full admin CMS with CKEditor 5, AJAX filters, FULLTEXT search, sanitized HTML, image processing, and a deployment-ready footprint.

---

## Live demo

- **Public site**: `https://<your-deployment-url>/`
- **Admin login**: `https://<your-deployment-url>/admin/login`

> The deployment URL is filled in after pushing to Render. See [Deployment](#deployment) below.

## Admin credentials

| Field    | Value                  |
| -------- | ---------------------- |
| Email    | `admin@jobyaari.com`   |
| Password | `Admin@12345`          |

Created by `database/seeders/AdminUserSeeder.php` on a fresh `db:seed`.

## Screenshots

| Page                  | Path                                |
| --------------------- | ----------------------------------- |
| Public landing        | `docs/screenshots/landing.png`      |
| AJAX filter in action | `docs/screenshots/filter.png`       |
| Admin dashboard       | `docs/screenshots/dashboard.png`    |
| Blog editor           | `docs/screenshots/editor.png`       |

> Capture these from your running deployment and drop into `docs/screenshots/`.

---

## Features

### Public

- **Hero + sticky filter bar**: category pills, date range (Today/Week/Month/Year/All), sort (Newest/Oldest/Most Viewed), debounced search.
- **AJAX-driven filters & pagination**: no page reloads. URL stays in sync via `history.pushState` so any filter combination is shareable.
- **Responsive grid**: 3 columns on desktop, 2 on tablet, 1 on mobile (tested at 375 px).
- **Blog detail page**: hero image, category badge, view counter (session-deduped), reading-time estimate, sanitized HTML content (tables, headings, lists, inline images), and Open Graph meta for shareable links.
- **Recently viewed**: last 5 blogs persisted in session as a fixed-size queue, shown on the listing page.
- **Share buttons**: WhatsApp, Twitter/X, LinkedIn, Copy Link — pure HTML/JS, zero third-party scripts.
- **Empty / loading / error states** on every async surface: friendly empty UI with "Clear filters", spinner overlay during AJAX, retry button on network failure.

### Admin

- **Login** with email + password, "Remember me", and **rate limiting** (5 attempts / minute / IP via `Illuminate\Support\Facades\RateLimiter`).
- **Dashboard**: 4 stat cards (total blogs, total categories, most-viewed, latest blog), quick-action buttons, and a Chart.js doughnut showing blogs per category.
- **Blog index**: searchable + sortable table, server-side pagination, thumbnail column, AJAX delete via SweetAlert2 confirmation with row fade-out.
- **Blog create / edit**: CKEditor 5 Classic (headings H1–H4, bold, italic, underline, lists, blockquote, **insertTable**, **imageUpload**, link, undo/redo, removeFormat, sourceEditing), live char counter on short description, live image preview on file pick.
- **Inline image upload** to `/admin/blogs/upload-image` — backed by Intervention Image v3, resized to ≤ 1200 px width, re-encoded to WebP with random filename.
- **Validation** via dedicated `StoreBlogRequest` / `UpdateBlogRequest` form requests.
- **Responsive admin**: sidebar becomes a slide-out drawer below 768 px.

---

## Tech stack

| Layer             | Choice                                                       |
| ----------------- | ------------------------------------------------------------ |
| Framework         | **Laravel 11.x**                                             |
| Language          | **PHP 8.2+** (developed against 8.3)                         |
| Database          | **MySQL 8 / MariaDB 10.11** (FULLTEXT + composite indexes)   |
| Frontend          | **Blade**, **Bootstrap 5.3** (CDN), **jQuery 3.7** (CDN)     |
| Rich text         | **CKEditor 5 Classic 41.4** (CDN) with custom upload adapter |
| Icons / chart     | **Font Awesome 6** (CDN), **Chart.js 4** (CDN)               |
| Confirm dialog    | **SweetAlert2** (CDN)                                        |
| Images            | **intervention/image v3** (GD driver, WebP output)           |
| HTML sanitization | **mews/purifier 3.4** (HTMLPurifier wrapper)                 |
| Auth              | Laravel's built-in `Auth` + custom `AdminAuth` middleware    |
| Typography        | One pinned font: **Inter** (400, 600) via Google Fonts       |

---

## Architecture decisions

A few choices that aren't obvious from reading the code:

### Why server-rendered Blade partials for AJAX

The `/api/blogs/filter` endpoint renders `public/partials/blog-card.blade.php` server-side and returns the HTML as part of the JSON payload. The frontend just swaps `$('#blogGrid').html(res.html)`. This keeps cards consistent between server-rendered first paint and AJAX re-renders, eliminates a parallel JS template, and ensures any Blade helper (Carbon dates, `route()`, `Str::limit`, etc.) works identically in both code paths.

### Why a composite + FULLTEXT index, not just plain ones

The most common public query is *"give me blogs in category X, ordered by `published_at DESC`, paginated."* A composite index on `(category_id, published_at)` lets MySQL/MariaDB satisfy both the `WHERE` and the `ORDER BY` from a single index scan — no filesort. The separate FULLTEXT index on `(title, short_description)` lets search use `MATCH ... AGAINST` in boolean mode (with a `*` wildcard suffix for prefix matches). For terms under 3 characters — below the FULLTEXT min-token-length — the service falls back to `LIKE` so behaviour stays correct.

### Why session-based view dedup, not cookie or IP

Storing seen blog IDs in the user's session (`viewed_blogs`) gives a per-browser, per-session deduplication for the view counter without leaking any PII server-side and without needing a separate `blog_views` table. Increment uses `DB::table('blogs')->where('id', $id)->increment('views')` — a single `UPDATE ... SET views = views + 1` — so concurrent views don't race. Tradeoff: a user who clears cookies will be re-counted; that's acceptable for this use case. For stricter dedup you'd add a `blog_views` table keyed on `(blog_id, ip, day)` or a Redis bloom filter.

### Why mews/purifier for HTML sanitization

CKEditor output is rendered with `{!! !!}`, which trusts the string. To make that safe, the controller passes content through `clean()` (mews/purifier → HTMLPurifier). The allowlist in `config/purifier.php` covers exactly the tags CKEditor produces (headings, lists, tables, images, links, blockquotes, formatting) while stripping `<script>`, inline JS, and `on*` attributes. This is more robust than a hand-rolled regex sanitizer and is the convention in the Laravel ecosystem.

### Why custom inline image upload over filerouter packages

CKEditor's image upload is wired through a tiny custom `UploadAdapter` in `resources/views/admin/blogs/_form_scripts.blade.php` that POSTs to `/admin/blogs/upload-image`. The endpoint validates MIME type + size, then delegates to `ImageUploadService`, which resizes to 1200 px max width and re-encodes to **WebP** with a randomized filename to prevent path traversal. Adding a heavy file-router package would be over-engineering for one endpoint.

### Why a fixed-size session queue for "recently viewed"

A simple `array_slice(array_unique(...), 0, 5)` after each detail-page visit gives an O(1) (well, O(5)) LRU-style queue without any storage. Survives the session, costs nothing, and the listing page just `whereIn`'s the IDs back into a single query.

### Why slug collision handling instead of random hashes

`Blog::generateUniqueSlug($title, $ignoreId)` appends `-2`, `-3`, … until unique. Readable slugs are better for SEO and for human reviewers; a random suffix would hide collisions and look amateur. The `$ignoreId` parameter ensures `update()` doesn't bump the slug of the row being updated.

### MariaDB-as-MySQL note

Default `config/database.php` for Laravel 11 sets `collation: utf8mb4_0900_ai_ci` (MySQL 8). MariaDB doesn't ship that collation. We switched the default to `utf8mb4_unicode_ci` in `config/database.php` (env-overridable as `DB_COLLATION`), which works on both servers and preserves all FULLTEXT / index features the schema relies on.

---

## Local setup

> Requires **PHP 8.2+**, **Composer**, and **MySQL 8 or MariaDB 10.11**.

```bash
# 1. Clone
git clone <your-repo-url> jobyaari-blog
cd jobyaari-blog

# 2. Install dependencies
composer install

# 3. Environment
cp .env.example .env
php artisan key:generate

# 4. Edit .env — at minimum:
#    DB_HOST, DB_PORT, DB_DATABASE (create empty DB), DB_USERNAME, DB_PASSWORD

# 5. Create DB:
mysql -u root -p -e "CREATE DATABASE jobyaari_blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 6. Migrate + seed (creates admin user, categories, 20 blogs with downloaded images)
php artisan migrate:fresh --seed

# 7. Serve
php artisan serve --host=127.0.0.1 --port=8000
```

Visit:

- Public site: <http://127.0.0.1:8000/>
- Admin login: <http://127.0.0.1:8000/admin/login>

The seeder downloads 20 placeholder blog images from `picsum.photos` into `public/uploads/blogs/`. If your machine has no outbound network access, image fields stay `NULL` and the model's `image_url` accessor falls back to a placeholder URL — the site still works, you just won't see distinct cover images.

---

## Database schema

```text
users
├── id, name, email (unique), password (bcrypt)
├── is_admin (bool, default false)
├── remember_token, email_verified_at
└── timestamps

categories
├── id, name (unique), slug (unique, indexed)
├── color (hex, for badge)
└── timestamps

blogs
├── id
├── title (indexed)
├── slug (unique, indexed)
├── short_description (string, 300 char)
├── content (longtext) — sanitized CKEditor HTML
├── image (string, nullable) — filename under public/uploads/blogs/
├── category_id (FK -> categories.id, cascade on delete)
├── views (unsigned bigint, default 0)
├── published_at (timestamp, indexed)
├── timestamps
├── INDEX (category_id, published_at)    -- composite
└── FULLTEXT (title, short_description)  -- search
```

---

## API reference

### `GET /api/blogs/filter`

Returns the filtered / searched / paginated grid as pre-rendered HTML plus metadata. Used by the public listing page; safe to call from anywhere that wants the same partials.

**Query parameters**

| Param        | Type   | Default  | Notes |
| ------------ | ------ | -------- | ----- |
| `category`   | string | `all`    | Category slug (`admit-card`, `latest-jobs`, `results`, `answer-key`, `syllabus`) or `all`. |
| `date_range` | string | `all`    | `today`, `week`, `month`, `year`, `all`. |
| `sort`       | string | `newest` | `newest`, `oldest`, `popular`. |
| `search`     | string | empty    | Free-text query. Uses FULLTEXT (`MATCH ... AGAINST` in boolean mode with trailing `*`) when ≥ 3 chars; falls back to `LIKE` otherwise. |
| `page`       | int    | `1`      | Page number. 9 per page. |

**Response**

```json
{
  "html":       "<article class=\"jy-card\">…</article>…",
  "pagination": "<nav class=\"jy-pagination\">…</nav>",
  "total":      42,
  "page":       1,
  "has_more":   true
}
```

`html` is a string of rendered `<article class="jy-card">` blocks. `pagination` is the rendered pagination nav. Drop both straight into the DOM.

---

## Deployment

The repo is set up for **Render** with two deployment paths.

### Option A — Render Web Service (Docker) + external managed MySQL

Render's free tier no longer includes MySQL. Use **Aiven** (free MySQL trial) or **Clever Cloud** (free MySQL up to 256 MB), then:

1. Create the MySQL database on your provider, copy connection details.
2. Push this repo to GitHub.
3. On Render, **New > Web Service**, select the repo. The blueprint (`render.yaml`) picks the Dockerfile automatically.
4. In Environment, set `APP_URL`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` from your MySQL provider. `APP_KEY` auto-generates from the blueprint.
5. Deploy. The Dockerfile runs `migrate --force` (and tries `db:seed --force`) on first boot.

### Option B — Heroku-style buildpack

The included `Procfile` (`web: vendor/bin/heroku-php-apache2 public/`) works on Heroku and any platform that respects Procfile. Provision a MySQL add-on and set the same `DB_*` env vars.

### Production checklist

- `APP_ENV=production`, `APP_DEBUG=false`
- `APP_KEY` set (generated once)
- `APP_URL` matches the deployment hostname (used for canonical + OG URLs)
- Storage writable: `chmod -R 775 storage bootstrap/cache public/uploads`
- After deploy: `php artisan config:cache route:cache view:cache`

---

## Future improvements

Honest, prioritized:

- **Redis cache** for the filter endpoint (hot keys: category × date × sort × page). Current MySQL queries are already index-served, but cache wins on shared filter combinations.
- **Cookie- or DB-backed view dedup** instead of session, so a logout doesn't reset a returning reader's view history.
- **In-memory trie autocomplete** on blog titles (cache for 1 h on boot), exposed at `/api/blogs/suggest?q=...`.
- **WYSIWYG image cropping** at upload time (crop 16:9 in browser before sending to backend) so cover images always frame cleanly.
- **Soft deletes** + admin trash bin so accidental deletes are recoverable.
- **Sitemap.xml + RSS** for SEO indexing of category and blog detail URLs.
- **PHPUnit feature tests** for the filter service, admin CRUD, and view-counter dedup. Skipped here in favour of a working end-to-end demo.

---

## Verification checklist

- [x] `php artisan migrate:fresh --seed` succeeds on a clean DB
- [x] Admin can log in with seeded credentials
- [x] Admin can create / edit / delete blogs
- [x] CKEditor toolbar includes headings, bullets, blockquote, table, inline image upload
- [x] AJAX delete works with SweetAlert2 confirm + row fade-out
- [x] Public listing pulls live from DB (no static blog data anywhere)
- [x] Category, date, sort, search filters all combine correctly via AJAX
- [x] URL stays in sync with filter state
- [x] Empty state shows when filters return zero results
- [x] Detail page increments view counter once per session
- [x] Related blogs render and are from the same category
- [x] Share buttons produce correct URLs (WhatsApp, X, LinkedIn, Copy Link)
- [x] Responsive at 375 px — no horizontal scroll
- [x] Admin sidebar collapses into a drawer on mobile
- [x] CSRF tokens on every form + AJAX header
- [x] Admin login rate-limited at 5 attempts / minute / IP
- [x] `.env` gitignored, `.env.example` committed with no secrets
- [x] README complete
