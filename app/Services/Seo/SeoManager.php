<?php

namespace App\Services\Seo;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * Collects SEO metadata for the current request and renders the
 * <head> meta tags (title, description, canonical, Open Graph,
 * Twitter card and Article JSON-LD).
 */
class SeoManager
{
    protected array $meta = [
        'title' => null,
        'description' => null,
        'image' => null,
        'url' => null,
        'type' => 'website',
        'published_time' => null,
        'author' => null,
        'section' => null,
    ];

    protected array $jsonLd = [];

    public function set(string $key, ?string $value): static
    {
        $this->meta[$key] = $value;

        return $this;
    }

    public function title(?string $title): static
    {
        return $this->set('title', $title);
    }

    public function description(?string $description): static
    {
        $clean = $description
            ? Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($description))), 160)
            : null;

        return $this->set('description', $clean);
    }

    public function image(?string $image): static
    {
        return $this->set('image', $image);
    }

    public function forHome(): static
    {
        $this->meta['type'] = 'website';
        $this->meta['url'] = url('/');
        $this->title(setting('site_name', config('app.name')));
        $this->description(setting('site_description', ''));

        return $this;
    }

    public function forPost(Post $post): static
    {
        $this->meta['type'] = 'article';
        $this->meta['url'] = $post->url();
        $this->meta['published_time'] = optional($post->published_at)->toIso8601String();
        $this->meta['author'] = optional($post->author)->name;
        $this->meta['section'] = $post->category?->getTranslation('name', app()->getLocale(), false);

        $this->title($post->getTranslation('meta_title', app()->getLocale(), false)
            ?: $post->getTranslation('title', app()->getLocale(), false));
        $this->description($post->getTranslation('meta_description', app()->getLocale(), false)
            ?: $post->getTranslation('excerpt', app()->getLocale(), false)
            ?: strip_tags((string) $post->getTranslation('body', app()->getLocale(), false)));
        $this->image($post->coverUrl('large'));

        $this->jsonLd = $this->articleSchema($post);

        return $this;
    }

    public function forCategory(Category $category): static
    {
        $this->meta['type'] = 'website';
        $this->meta['url'] = $category->url();
        $this->title($category->getTranslation('meta_title', app()->getLocale(), false)
            ?: $category->getTranslation('name', app()->getLocale(), false));
        $this->description($category->getTranslation('meta_description', app()->getLocale(), false)
            ?: $category->getTranslation('description', app()->getLocale(), false));

        return $this;
    }

    public function render(): HtmlString
    {
        $siteName = setting('site_name', config('app.name'));
        $title = $this->meta['title'] ? $this->meta['title'].' — '.$siteName : $siteName;
        $desc = $this->meta['description'] ?? setting('site_description', '');
        $url = $this->meta['url'] ?? url()->current();
        $image = $this->meta['image'] ?? setting('default_share_image');
        $type = $this->meta['type'];

        $tags = [];
        $tags[] = '<title>'.e($title).'</title>';
        $tags[] = '<meta name="description" content="'.e($desc).'">';
        $tags[] = '<link rel="canonical" href="'.e($url).'">';

        // Open Graph
        $tags[] = '<meta property="og:site_name" content="'.e($siteName).'">';
        $tags[] = '<meta property="og:title" content="'.e($title).'">';
        $tags[] = '<meta property="og:description" content="'.e($desc).'">';
        $tags[] = '<meta property="og:type" content="'.e($type).'">';
        $tags[] = '<meta property="og:url" content="'.e($url).'">';
        $tags[] = '<meta property="og:locale" content="'.e(app()->getLocale()).'">';
        if ($image) {
            $tags[] = '<meta property="og:image" content="'.e($image).'">';
        }
        if ($type === 'article') {
            if ($this->meta['published_time']) {
                $tags[] = '<meta property="article:published_time" content="'.e($this->meta['published_time']).'">';
            }
            if ($this->meta['section']) {
                $tags[] = '<meta property="article:section" content="'.e($this->meta['section']).'">';
            }
        }

        // Twitter
        $tags[] = '<meta name="twitter:card" content="'.($image ? 'summary_large_image' : 'summary').'">';
        $tags[] = '<meta name="twitter:title" content="'.e($title).'">';
        $tags[] = '<meta name="twitter:description" content="'.e($desc).'">';
        if ($image) {
            $tags[] = '<meta name="twitter:image" content="'.e($image).'">';
        }
        if ($handle = setting('twitter_handle')) {
            $tags[] = '<meta name="twitter:site" content="'.e($handle).'">';
        }

        if (! empty($this->jsonLd)) {
            $tags[] = '<script type="application/ld+json">'.json_encode($this->jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).'</script>';
        }

        return new HtmlString(implode("\n    ", $tags));
    }

    protected function articleSchema(Post $post): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $post->getTranslation('title', app()->getLocale(), false),
            'datePublished' => optional($post->published_at)->toIso8601String(),
            'dateModified' => optional($post->updated_at)->toIso8601String(),
            'author' => ['@type' => 'Person', 'name' => optional($post->author)->name],
            'publisher' => [
                '@type' => 'Organization',
                'name' => setting('site_name', config('app.name')),
            ],
            'mainEntityOfPage' => $post->url(),
            'image' => array_filter([$post->coverUrl('large')]),
        ];
    }
}
