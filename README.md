# বার্তা — Barta News Portal CMS

> **বার্তা** একটি সম্পূর্ণ, WordPress-মানের বহুভাষিক (বাংলা + ইংরেজি) নিউজ পোর্টাল CMS — তৈরি হয়েছে **Laravel 13** এবং **Livewire 3** দিয়ে।
>
> **Barta** is a complete, WordPress-class multilingual (Bangla + English) news‑portal CMS built with **Laravel 13** and **Livewire 3** — with theme & plugin engines, a premium/paywall system, Bangladeshi payment gateways (SSLCommerz + bKash), AI assistance, a media manager, SEO tooling and more.

<p align="center">
  <img src="themes/barta/assets/screenshot.svg" alt="Barta theme screenshot" width="640">
</p>

---

## সূচিপত্র · Table of contents

- [এক নজরে · Overview](#এক-নজরে--overview)
- [ফিচার · Features](#ফিচার--features)
- [প্রযুক্তি · Tech stack](#প্রযুক্তি--tech-stack)
- [সিস্টেম রিকোয়ারমেন্ট · Requirements](#সিস্টেম-রিকোয়ারমেন্ট--requirements)
- [ইনস্টলেশন · Installation](#ইনস্টলেশন--installation)
- [ডিফল্ট লগইন · Default logins](#ডিফল্ট-লগইন--default-logins)
- [কনফিগারেশন · Configuration](#কনফিগারেশন--configuration)
- [প্রজেক্ট স্ট্রাকচার · Project structure](#প্রজেক্ট-স্ট্রাকচার--project-structure)
- [থিম তৈরি · Theme development](#থিম-তৈরি--theme-development)
- [প্লাগইন তৈরি · Plugin development](#প্লাগইন-তৈরি--plugin-development)
- [রোল ও পারমিশন · Roles & permissions](#রোল-ও-পারমিশন--roles--permissions)
- [পেমেন্ট · Payments](#পেমেন্ট--payments)
- [প্রোডাকশন ডিপ্লয় · Deployment](#প্রোডাকশন-ডিপ্লয়--deployment)
- [দরকারি কমান্ড · Useful commands](#দরকারি-কমান্ড--useful-commands)
- [লাইসেন্স · License](#লাইসেন্স--license)

---

## এক নজরে · Overview

বার্তা একটি production-ready নিউজ পোর্টাল যা WordPress-এর মতো নমনীয় — কিন্তু আধুনিক Laravel + Livewire স্ট্যাকের উপর তৈরি। কন্টেন্ট (পোস্ট, ক্যাটাগরি, ট্যাগ, মেনু) বাংলা ও ইংরেজি — দুই ভাষায় সংরক্ষণ হয়। থিম ও প্লাগইন WordPress-এর মতোই আলাদা ফোল্ডারে ড্রপ করে অ্যাক্টিভেট করা যায়। প্রিমিয়াম কন্টেন্টের জন্য পেওয়াল ও বাংলাদেশি পেমেন্ট গেটওয়ে (SSLCommerz, bKash) বিল্ট-ইন।

Barta is a production-ready news portal that is as flexible as WordPress, but built on a modern Laravel + Livewire foundation. Content (posts, categories, tags, menus) is stored in both Bangla and English. Themes and plugins are dropped into their own folders and activated from the admin panel, exactly like WordPress. A paywall with Bangladeshi payment gateways is built in.

---

## ফিচার · Features

**কন্টেন্ট ব্যবস্থাপনা · Content management**

- পোস্ট / সংবাদ ও স্ট্যাটিক পেজ, ড্রাফট → পেন্ডিং → পাবলিশড → শিডিউলড ওয়ার্কফ্লো
- ক্যাটাগরি (নেস্টেড / প্যারেন্ট‑চাইল্ড) ও ট্যাগ
- কমেন্ট মডারেশন (approve / spam / trash), থ্রেডেড রিপ্লাই
- মিডিয়া লাইব্রেরি (spatie/laravel-medialibrary, অটো thumbnail/large conversions)
- ফিচার্ড, ব্রেকিং ও প্রিমিয়াম পোস্ট ফ্ল্যাগ

**বহুভাষিক · Multilingual (bn + en)**

- অনুবাদযোগ্য ফিল্ড JSON কলামে (spatie/laravel-translatable)
- ফ্রন্টএন্ড ভাষা সুইচার (`/lang/bn`, `/lang/en`)
- বাংলা সংখ্যা রূপান্তর হেল্পার (`to_bn_number`, `localized_number`)

**SEO ও সোশ্যাল · SEO & social**

- প্রতি পোস্ট/পেজ/ক্যাটাগরিতে meta title/description
- Open Graph + Twitter Card ট্যাগ, JSON-LD structured data
- অটো `sitemap.xml` (spatie/laravel-sitemap)
- নমনীয় permalink স্ট্রাকচার (WordPress-এর মতো)

**মনিটাইজেশন · Monetisation**

- বিজ্ঞাপন ব্যবস্থাপনা (ad slots: header, home-hero, in-article, sidebar, footer; image/html/AdSense)
- প্রিমিয়াম কন্টেন্ট পেওয়াল + সাবস্ক্রিপশন প্ল্যান (মাসিক / বাৎসরিক / lifetime)
- বাংলাদেশি পেমেন্ট গেটওয়ে: **SSLCommerz** ও **bKash** (ডিফল্ট sandbox)

**অ্যাপিয়ারেন্স · Appearance**

- WordPress-স্টাইল **থিম সিস্টেম** (`themes/` ফোল্ডার, JSON manifest, admin থেকে অ্যাক্টিভেট)
- **উইজেট** ও **মেনু** বিল্ডার (drag-free, area/location-ভিত্তিক)
- হোমপেজ উইজেট এরিয়া (popular/recent posts, category list, tag cloud, newsletter, ad, html)
- ডিফল্ট থিম: আধুনিক বাংলা দৈনিক-অনুপ্রাণিত রেসপন্সিভ লেআউট (লাল অ্যাকসেন্ট, Hind Siliguri / Tiro Bangla টাইপ)

**এক্সটেনসিবিলিটি · Extensibility**

- WordPress-স্টাইল **প্লাগইন সিস্টেম** (`plugins/` ফোল্ডার) — action/filter **hooks** সহ
- উদাহরণ প্লাগইন: `ReadingProgress`
- **AI সহায়তা** (OpenAI-compatible: OpenAI, OpenRouter, Groq, লোকাল Ollama — `.env`-এ কনফিগারযোগ্য) — শিরোনাম/সারাংশ/SEO জেনারেশন

**অডিয়েন্স · Audience**

- নিউজলেটার (double opt-in, verify/unsubscribe, one-click List-Unsubscribe)
- ব্রেকিং নিউজ ইমেইল অ্যালার্ট (কিউ-ড্রিভেন, chunked)

**ব্যবহারকারী · Users & security**

- ৬টি রোল (super-admin → subscriber), granular permissions (spatie/laravel-permission)
- Livewire অথেন্টিকেশন (login/register/forgot/reset)
- স্টাফ-only অ্যাডমিন প্যানেল

---

## প্রযুক্তি · Tech stack

| স্তর · Layer | প্রযুক্তি · Technology |
| --- | --- |
| ফ্রেমওয়ার্ক | Laravel 13 (PHP 8.3+) |
| UI / interactivity | Livewire 3, Blade, Tailwind CSS, Vite |
| অনুবাদ | spatie/laravel-translatable |
| রোল/পারমিশন | spatie/laravel-permission |
| মিডিয়া | spatie/laravel-medialibrary, intervention/image |
| SEO | spatie/laravel-sitemap + কাস্টম `SeoManager` |
| slug | spatie/laravel-sluggable |
| HTTP client | guzzlehttp/guzzle (AI ও পেমেন্ট কল) |
| ডেটাবেস | MySQL 8 / MariaDB 10.6+ (অথবা quick spin-up-এ SQLite) |

---

## সিস্টেম রিকোয়ারমেন্ট · Requirements

- **PHP 8.3+** with extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd` (বা `imagick`), `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`
- **Composer 2.x**
- **Node.js 18+** ও **npm** (ফ্রন্টএন্ড অ্যাসেট বিল্ডের জন্য)
- **MySQL 8** / **MariaDB 10.6+** (অথবা SQLite ৩)
- একটি queue worker চালানোর সামর্থ্য (নিউজলেটার/অ্যালার্টের জন্য — ঐচ্ছিক তবে প্রস্তাবিত)

---

## ইনস্টলেশন · Installation

> নিচের ধাপগুলো আপনার নিজের মেশিন/সার্ভারে চালাবেন। বার্তা **সোর্স কোড** হিসেবে ডেলিভার হয় — আপনি নিজেই রান করেন।

```bash
# 1) ডিপেন্ডেন্সি ইনস্টল · Install PHP & JS dependencies
composer install
npm install

# 2) এনভায়রনমেন্ট ফাইল · Environment file
cp .env.example .env
php artisan key:generate

# 3) .env-এ ডেটাবেস তথ্য দিন · Set DB credentials in .env
#    DB_DATABASE=barta  DB_USERNAME=...  DB_PASSWORD=...
#    (দ্রুত টেস্টের জন্য DB_CONNECTION=sqlite দিতে পারেন)

# 4) মাইগ্রেশন + ডেমো কন্টেন্ট · Migrate & seed demo content
php artisan migrate --seed

# 5) স্টোরেজ লিংক (মিডিয়া) · Symlink storage
php artisan storage:link

# 6) ফ্রন্টএন্ড অ্যাসেট বিল্ড · Build assets
npm run build      # অথবা ডেভেলপমেন্টে: npm run dev

# 7) সার্ভার চালু · Serve
php artisan serve
```

সাইট: <http://localhost:8000> · অ্যাডমিন প্যানেল: <http://localhost:8000/admin>

কিউ ও শিডিউলার (ঐচ্ছিক কিন্তু নিউজলেটার/অ্যালার্ট/শিডিউলড পোস্টের জন্য দরকার):

```bash
php artisan queue:work        # background jobs (newsletter, breaking-news alerts)
php artisan schedule:work     # scheduled publishing
```

---

## ডিফল্ট লগইন · Default logins

`migrate --seed` চালানোর পর নিচের ডেমো অ্যাকাউন্টগুলো তৈরি হয় (**সব পাসওয়ার্ড: `password`**)। প্রোডাকশনে যাওয়ার আগে অবশ্যই পাসওয়ার্ড পরিবর্তন করুন।

| রোল · Role | ইমেইল · Email | পাসওয়ার্ড |
| --- | --- | --- |
| Super Admin | `admin@barta.test` | `password` |
| Editor | `editor@barta.test` | `password` |
| Author | `tanvir@barta.test` | `password` |
| Author | `nusrat@barta.test` | `password` |
| Author | `shakil@barta.test` | `password` |
| Subscriber | `reader@barta.test` | `password` |

---

## কনফিগারেশন · Configuration

সব সেটিং `.env` ও `config/barta.php`-এ। মূল অংশগুলো:

**ভাষা · Locales**

```env
APP_LOCALE=bn            # ডিফল্ট ভাষা
APP_FALLBACK_LOCALE=en
BARTA_LOCALES=bn,en      # সক্রিয় ভাষাসমূহ (কমা-বিভক্ত)
```

**Permalink** — WordPress-এর মতো URL স্ট্রাকচার (admin → Settings থেকেও বদলানো যায়):

```env
BARTA_PERMALINK=date     # default | date | day | month | postname | category
```

| মান | উদাহরণ URL |
| --- | --- |
| `default` | `/news/{slug}` |
| `date` | `/2026/08/{slug}` |
| `day` | `/2026/08/23/{slug}` |
| `postname` | `/{slug}` |
| `category` | `/{category}/{slug}` |

**AI সহায়তা** — যেকোনো OpenAI-compatible এন্ডপয়েন্ট:

```env
AI_ENABLED=false
AI_DRIVER=openai
AI_BASE_URL=https://api.openai.com/v1
AI_API_KEY=              # আপনার নিজের কী — রিপোজিটরিতে কমিট করবেন না
AI_MODEL=gpt-4o-mini
```

**পেমেন্ট · Payments** (ডিফল্ট sandbox):

```env
PAYMENT_CURRENCY=BDT
PAYMENT_DEFAULT_GATEWAY=sslcommerz

SSLCZ_STORE_ID=
SSLCZ_STORE_PASSWORD=
SSLCZ_SANDBOX=true

BKASH_APP_KEY=
BKASH_APP_SECRET=
BKASH_USERNAME=
BKASH_PASSWORD=
BKASH_SANDBOX=true
```

> ⚠️ **নিরাপত্তা · Security:** API key, store password, পেমেন্ট secret — কোনো credential **কখনো** Git-এ কমিট করবেন না। `.env` ইতিমধ্যে `.gitignore`-এ আছে। প্রোডাকশন secret সার্ভারের এনভায়রনমেন্টে রাখুন।

---

## প্রজেক্ট স্ট্রাকচার · Project structure

```
barta-cms/
├── app/
│   ├── Console/Commands/          # artisan কমান্ড
│   ├── Http/
│   │   ├── Controllers/           # ফ্রন্টএন্ড কন্ট্রোলার (Home, Post, Category, Payment…)
│   │   └── Middleware/            # SetLocale, EnsureUserIsStaff, EnsureSubscribed
│   ├── Jobs/                      # SendNewsletter, SendBreakingNewsAlert
│   ├── Livewire/
│   │   ├── Admin/                 # অ্যাডমিন প্যানেলের সব Livewire কম্পোনেন্ট
│   │   ├── Auth/                  # login/register/forgot/reset
│   │   └── Front/                 # Plans (সাবস্ক্রিপশন)
│   ├── Mail/ · Notifications/     # ইমেইল ও নোটিফিকেশন
│   ├── Models/                    # 18টি Eloquent মডেল
│   ├── Observers/                 # PostObserver (ব্রেকিং নিউজ অ্যালার্ট ট্রিগার)
│   ├── Services/
│   │   ├── Ai/                    # AiService (OpenAI-compatible)
│   │   ├── Payment/               # PaymentManager + SSLCommerz/bKash gateways
│   │   ├── Plugin/                # PluginManager + HookManager
│   │   ├── Theme/                 # ThemeManager
│   │   ├── Seo/                   # SeoManager
│   │   ├── PermalinkService.php
│   │   └── SettingsRepository.php
│   └── Support/helpers.php        # গ্লোবাল হেল্পার (setting(), money(), excerpt()…)
├── config/barta.php               # CMS কনফিগ (locales, permalinks, roles, themes…)
├── database/
│   ├── migrations/                # 21টি মাইগ্রেশন
│   ├── factories/                 # 5টি factory
│   └── seeders/                   # 12টি seeder + DatabaseSeeder
├── routes/
│   ├── web.php                    # পাবলিক + auth + payment রুট
│   └── admin.php                  # /admin (staff-only)
├── themes/
│   └── barta/                     # ডিফল্ট থিম (views + assets + theme.json)
├── plugins/
│   └── ReadingProgress/           # উদাহরণ প্লাগইন
├── lang/                          # bn / en অনুবাদ ফাইল
├── resources/                     # অ্যাডমিন layout, Livewire views, css/js
└── public/                        # ওয়েব রুট
```

---

## থিম তৈরি · Theme development

WordPress-এর মতো — `themes/` ফোল্ডারে একটি নতুন ফোল্ডার তৈরি করুন, তাতে একটি `theme.json` manifest ও Blade views দিন, তারপর অ্যাডমিন → **Appearance → Themes** থেকে অ্যাক্টিভেট করুন।

```
themes/your-theme/
├── theme.json          # নাম, slug, version, supports (widget areas, menus, ad slots)
├── views/
│   ├── layouts/app.blade.php
│   ├── home.blade.php
│   ├── post.blade.php
│   ├── partials/…
│   └── …
└── assets/             # css/js/images (theme_asset() হেল্পার দিয়ে লোড)
```

থিম ভিউ `theme::` namespace-এ পাওয়া যায় (`@extends('theme::layouts.app')`)। থিম-বহির্ভূত অ্যাসেট `theme_asset('css/app.css')` হেল্পার দিয়ে সার্ভ হয়।

---

## প্লাগইন তৈরি · Plugin development

`plugins/` ফোল্ডারে একটি ফোল্ডার + `plugin.json` manifest + একটি ServiceProvider দিন। প্লাগইন **hooks** (action/filter) দিয়ে কোর কোড না ছুঁয়েই আচরণ পরিবর্তন করতে পারে।

```php
// একটি action-এ যুক্ত হওয়া · Hook into an action
add_action('post.rendered', function ($post) {
    // ...
});

// আউটপুট ফিল্টার করা · Filter output
add_filter('post.body', fn ($html, $post) => $html.$extra);
```

`ReadingProgress` প্লাগইনটি একটি কার্যকরী উদাহরণ — দেখুন `plugins/ReadingProgress/`। অ্যাডমিন → **Appearance → Plugins** থেকে অ্যাক্টিভেট/ডিঅ্যাক্টিভেট করুন।

---

## রোল ও পারমিশন · Roles & permissions

| রোল | সংক্ষিপ্ত সামর্থ্য · Capability |
| --- | --- |
| **super-admin** | সবকিছু (সব permission) |
| **admin** | সব কন্টেন্ট + ইউজার + সেটিংস |
| **editor** | পোস্ট পাবলিশ, ক্যাটাগরি/ট্যাগ/কমেন্ট/মিডিয়া/মেনু/উইজেট |
| **author** | নিজের পোস্ট লেখা ও পাবলিশ, মিডিয়া |
| **contributor** | পোস্ট খসড়া লেখা (পাবলিশ নয়), মিডিয়া |
| **subscriber** | কেবল পড়া / প্রিমিয়াম অ্যাক্সেস (কোনো অ্যাডমিন অ্যাক্সেস নেই) |

`/admin` প্যানেল কেবল **staff** রোলের জন্য (subscriber ছাড়া বাকি সব)। ইউজার ব্যবস্থাপনা ও সেটিংসে অতিরিক্ত permission গার্ড আছে। কনফিগ: `config/barta.php`।

---

## পেমেন্ট · Payments

প্রিমিয়াম কন্টেন্ট ও সাবস্ক্রিপশন প্ল্যানের জন্য দুটি বাংলাদেশি গেটওয়ে বিল্ট-ইন:

- **SSLCommerz** (কার্ড, মোবাইল ব্যাংকিং, নেট ব্যাংকিং)
- **bKash** (checkout API)

দুটোই ডিফল্টে **sandbox** মোডে থাকে। লাইভে যেতে `.env`-এ আপনার আসল credential দিন এবং `*_SANDBOX=false` করুন। গেটওয়ে callback (return/IPN) রুটগুলো CSRF-exempt ও auth-এর বাইরে রাখা — কারণ cross-site POST return সবসময় সেশন কুকি বহন করে না; এগুলো `{payment}` reference থেকে ইউজার/প্ল্যান re-resolve করে।

> নতুন গেটওয়ে যোগ করতে: `app/Services/Payment/Contracts/PaymentGateway.php` ইন্টারফেস ইমপ্লিমেন্ট করে `PaymentManager`-এ রেজিস্টার করুন।

---

## প্রোডাকশন ডিপ্লয় · Deployment

```bash
composer install --no-dev --optimize-autoloader
npm run build
php artisan migrate --force
php artisan config:cache route:cache view:cache
php artisan storage:link
```

- `APP_ENV=production`, `APP_DEBUG=false` সেট করুন
- একটি process manager (supervisor/systemd) দিয়ে `php artisan queue:work` চালু রাখুন
- ক্রন-এ যোগ করুন: `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1`
- ওয়েব রুট `public/` ডিরেক্টরিতে পয়েন্ট করান

---

## দরকারি কমান্ড · Useful commands

```bash
php artisan migrate:fresh --seed     # ডেটাবেস রিসেট + ডেমো কন্টেন্ট
php artisan queue:work                # background jobs
php artisan schedule:work             # শিডিউলার (ডেভে)
php artisan optimize:clear            # সব cache ক্লিয়ার
./vendor/bin/pint                     # কোড স্টাইল ফিক্স (Laravel Pint)
php artisan test                      # টেস্ট রান
```

---

## লাইসেন্স · License

MIT License — অবাধে ব্যবহার, পরিবর্তন ও বিতরণযোগ্য। বিস্তারিত: [`LICENSE`](LICENSE)।

---

<p align="center"><sub>বার্তা · Barta — Laravel 13 · Livewire 3 দিয়ে ❤️ সহকারে তৈরি।</sub></p>
