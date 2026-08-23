<?php

namespace App\Livewire\Admin\Posts;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\Ai\AiService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('Edit post')]
class PostForm extends Component
{
    use WithFileUploads;

    public ?Post $post = null;

    public string $activeLocale = 'bn';

    // Translatable fields keyed by locale.
    public array $title = [];
    public array $excerpt = [];
    public array $body = [];
    public array $metaTitle = [];
    public array $metaDescription = [];

    public ?int $category_id = null;
    public string $tagsInput = '';
    public string $slug = '';
    public string $type = 'post';
    public string $status = 'draft';
    public string $format = 'standard';
    public bool $is_premium = false;
    public bool $is_featured = false;
    public bool $is_breaking = false;
    public bool $allow_comments = true;
    public ?string $source = null;
    public ?string $source_url = null;
    public ?string $video_url = null;
    public ?string $published_at = null;

    public $cover;                 // freshly uploaded file
    public ?string $existingCover = null;
    public bool $removeCover = false;
    public bool $showMediaLibrary = false;
    public ?string $selectedMediaPath = null;
    public ?string $selectedMediaUrl = null;

    public string $aiMessage = '';
    public array $aiTitleOptions = [];

    public function mount(?Post $post = null): void
    {
        $this->activeLocale = config('barta.default_locale', 'bn');

        foreach (barta_locales() as $loc) {
            $this->title[$loc] = '';
            $this->excerpt[$loc] = '';
            $this->body[$loc] = '';
            $this->metaTitle[$loc] = '';
            $this->metaDescription[$loc] = '';
        }

        if ($post && $post->exists) {
            $this->post = $post->load('tags');
            foreach (barta_locales() as $loc) {
                $this->title[$loc] = $post->getTranslation('title', $loc, false);
                $this->excerpt[$loc] = $post->getTranslation('excerpt', $loc, false);
                $this->body[$loc] = $post->getTranslation('body', $loc, false);
                $this->metaTitle[$loc] = $post->getTranslation('meta_title', $loc, false);
                $this->metaDescription[$loc] = $post->getTranslation('meta_description', $loc, false);
            }
            $this->category_id = $post->category_id;
            $this->tagsInput = $post->tags->map(fn ($t) => $t->getTranslation('name', $this->activeLocale, false) ?: $t->name)->implode(', ');
            $this->slug = $post->slug;
            $this->type = $post->type;
            $this->status = $post->status;
            $this->format = $post->format;
            $this->is_premium = $post->is_premium;
            $this->is_featured = $post->is_featured;
            $this->is_breaking = $post->is_breaking;
            $this->allow_comments = $post->allow_comments;
            $this->source = $post->source;
            $this->source_url = $post->source_url;
            $this->video_url = $post->video_url;
            $this->published_at = $post->published_at?->format('Y-m-d\TH:i');
            $this->existingCover = $post->coverUrl('thumb');
        }
    }

    protected function rules(): array
    {
        $default = config('barta.default_locale', 'bn');

        return [
            "title.$default" => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'slug' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:post,page'],
            'status' => ['required', 'in:draft,published,scheduled,pending'],
            'cover' => ['nullable', 'image', 'max:5120'],
            'video_url' => ['nullable', 'url'],
            'source_url' => ['nullable', 'url'],
        ];
    }

    public function save(bool $andPublish = false)
    {
        if ($this->selectedMediaPath && ! $this->isLibraryImage($this->selectedMediaPath)) {
            $this->addError('cover', __('The selected media file is no longer available.'));

            return;
        }

        if ($andPublish) {
            $this->status = 'published';
        }

        $this->validate();

        $post = $this->post ?? new Post(['user_id' => auth()->id()]);

        $post->setTranslations('title', $this->clean($this->title));
        $post->setTranslations('excerpt', $this->clean($this->excerpt));
        $post->setTranslations('body', $this->body);
        $post->setTranslations('meta_title', $this->clean($this->metaTitle));
        $post->setTranslations('meta_description', $this->clean($this->metaDescription));

        $post->category_id = $this->category_id;
        $post->type = $this->type;
        $post->status = $this->status;
        $post->format = $this->format;
        $post->is_premium = $this->is_premium;
        $post->is_featured = $this->is_featured;
        $post->is_breaking = $this->is_breaking;
        $post->allow_comments = $this->allow_comments;
        $post->source = $this->source;
        $post->source_url = $this->source_url;
        $post->video_url = $this->video_url;

        if (in_array($this->status, ['published', 'scheduled'])) {
            $post->published_at = $this->published_at ? Carbon::parse($this->published_at) : now();
        }

        // Allow a manual slug override.
        if (filled($this->slug)) {
            $post->slug = Str::slug($this->slug) ?: $this->slug;
        }

        $post->save();

        $this->syncTags($post);

        if ($this->removeCover || $this->cover || $this->selectedMediaPath) {
            $post->clearMediaCollection('featured');
        }
        if ($this->cover) {
            $fileName = Str::random(8).'-'.Str::slug(pathinfo($this->cover->getClientOriginalName(), PATHINFO_FILENAME))
                .'.'.$this->cover->getClientOriginalExtension();
            $path = $this->cover->storeAs('media', $fileName, 'public');

            $post->addMediaFromDisk($path, 'public')
                ->preservingOriginal()
                ->toMediaCollection('featured');
        } elseif ($this->selectedMediaPath) {
            $post->addMediaFromDisk($this->selectedMediaPath, 'public')
                ->preservingOriginal()
                ->toMediaCollection('featured');
        }

        session()->flash('status', __('Post saved.'));

        return redirect()->route('admin.posts.edit', $post);
    }

    protected function syncTags(Post $post): void
    {
        $names = collect(explode(',', $this->tagsInput))
            ->map(fn ($n) => trim($n))
            ->filter()
            ->unique();

        $ids = $names->map(function ($name) {
            return Tag::where('name->'.$this->activeLocale, $name)
                ->first()?->id
                ?? Tag::create(['name' => [$this->activeLocale => $name]])->id;
        });

        $post->tags()->sync($ids->all());
    }

    protected function clean(array $values): array
    {
        return array_filter($values, fn ($v) => filled($v));
    }

    public function openMediaLibrary(): void
    {
        $this->showMediaLibrary = true;
    }

    public function selectMedia(string $path): void
    {
        if (! $this->isLibraryImage($path)) {
            $this->addError('cover', __('The selected media file is not a valid image.'));

            return;
        }

        $this->selectedMediaPath = $path;
        $this->selectedMediaUrl = Storage::disk('public')->url($path);
        $this->cover = null;
        $this->removeCover = false;
        $this->showMediaLibrary = false;
        $this->resetErrorBag('cover');
    }

    protected function isLibraryImage(string $path): bool
    {
        return Str::startsWith($path, 'media/')
            && Storage::disk('public')->exists($path)
            && in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif']);
    }

    protected function mediaImages(): array
    {
        return collect(Storage::disk('public')->files('media'))
            ->filter(fn (string $path) => $this->isLibraryImage($path))
            ->sortDesc()
            ->take(60)
            ->map(fn (string $path) => [
                'path' => $path,
                'name' => basename($path),
                'url' => Storage::disk('public')->url($path),
            ])
            ->values()
            ->all();
    }

    // -----------------------------------------------------------------
    // AI assistance
    // -----------------------------------------------------------------
    public function aiSuggestTitles(AiService $ai): void
    {
        $this->runAi($ai, function () use ($ai) {
            $this->aiTitleOptions = $ai->suggestTitles($this->body[$this->activeLocale] ?? '', $this->activeLocale);
        });
    }

    public function useTitle(string $value): void
    {
        $this->title[$this->activeLocale] = $value;
        $this->aiTitleOptions = [];
    }

    public function aiSummarize(AiService $ai): void
    {
        $this->runAi($ai, function () use ($ai) {
            $this->excerpt[$this->activeLocale] = $ai->summarize($this->body[$this->activeLocale] ?? '', $this->activeLocale);
        });
    }

    public function aiTags(AiService $ai): void
    {
        $this->runAi($ai, function () use ($ai) {
            $this->tagsInput = implode(', ', $ai->suggestTags($this->body[$this->activeLocale] ?? '', $this->activeLocale));
        });
    }

    public function aiTranslate(AiService $ai, string $to): void
    {
        $this->runAi($ai, function () use ($ai, $to) {
            $from = $to === 'en' ? 'bn' : 'en';
            if (filled($this->title[$from] ?? '')) {
                $this->title[$to] = $ai->translate($this->title[$from], $to, $from);
            }
            if (filled($this->body[$from] ?? '')) {
                $this->body[$to] = $ai->translate($this->body[$from], $to, $from);
            }
            $this->aiMessage = __('Translated to :lang.', ['lang' => locale_name($to)]);
        });
    }

    /** Run an AI action with a consistent enabled-check and error surface. */
    protected function runAi(AiService $ai, callable $action): void
    {
        $this->resetErrorBag('ai');
        $this->aiMessage = '';

        if (! $ai->enabled()) {
            $this->addError('ai', __('AI is disabled. Set AI_ENABLED=true and AI_API_KEY in your .env.'));

            return;
        }

        try {
            $action();
        } catch (\Throwable $e) {
            $this->addError('ai', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.posts.form', [
            'categories' => Category::orderBy('id')->get(),
            'locales' => barta_locales(),
            'mediaImages' => $this->showMediaLibrary ? $this->mediaImages() : [],
        ]);
    }
}
