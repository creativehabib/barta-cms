<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;

/**
 * Seeds a realistic Bengali news taxonomy: seven top-level sections (a few with
 * sub-sections) and a set of tags. Slugs are forced to clean ASCII values —
 * Str::slug() can't transliterate Bangla, so we set the slug explicitly after
 * create (safe because the models use doNotGenerateSlugsOnUpdate).
 */
class TaxonomySeeder extends Seeder
{
    public function run(): void
    {
        // --- Top-level sections ---------------------------------------------
        $national = $this->category('national', ['bn' => 'জাতীয়', 'en' => 'National'], null, 1, '#c81420', 'flag', ['bn' => 'দেশের রাজনীতি, সরকার ও সমাজের খবর।', 'en' => 'National politics, government and society.']);
        $international = $this->category('international', ['bn' => 'আন্তর্জাতিক', 'en' => 'International'], null, 2, '#1d4ed8', 'globe', ['bn' => 'বিশ্বজুড়ে সর্বশেষ ঘটনা।', 'en' => 'The latest from around the world.']);
        $sports = $this->category('sports', ['bn' => 'খেলা', 'en' => 'Sports'], null, 3, '#059669', 'trophy', ['bn' => 'ক্রিকেট, ফুটবলসহ সব খেলার খবর।', 'en' => 'Cricket, football and all sports.']);
        $economy = $this->category('economy', ['bn' => 'অর্থনীতি', 'en' => 'Economy'], null, 4, '#b45309', 'chart', ['bn' => 'ব্যবসা, বাজার ও অর্থনীতির বিশ্লেষণ।', 'en' => 'Business, markets and the economy.']);
        $entertainment = $this->category('entertainment', ['bn' => 'বিনোদন', 'en' => 'Entertainment'], null, 5, '#7c3aed', 'film', ['bn' => 'চলচ্চিত্র, সংগীত ও তারকাদের খবর।', 'en' => 'Film, music and celebrities.']);
        $technology = $this->category('technology', ['bn' => 'প্রযুক্তি', 'en' => 'Technology'], null, 6, '#0891b2', 'chip', ['bn' => 'প্রযুক্তি, বিজ্ঞান ও উদ্ভাবন।', 'en' => 'Tech, science and innovation.']);
        $opinion = $this->category('opinion', ['bn' => 'মতামত', 'en' => 'Opinion'], null, 7, '#475569', 'pen', ['bn' => 'সম্পাদকীয় ও বিশ্লেষণধর্মী মতামত।', 'en' => 'Editorials and analysis.']);

        // --- Sub-sections ----------------------------------------------------
        $this->category('politics', ['bn' => 'রাজনীতি', 'en' => 'Politics'], $national->id, 1);
        $this->category('crime', ['bn' => 'অপরাধ', 'en' => 'Crime'], $national->id, 2);
        $this->category('south-asia', ['bn' => 'দক্ষিণ এশিয়া', 'en' => 'South Asia'], $international->id, 1);
        $this->category('cricket', ['bn' => 'ক্রিকেট', 'en' => 'Cricket'], $sports->id, 1);
        $this->category('football', ['bn' => 'ফুটবল', 'en' => 'Football'], $sports->id, 2);

        // --- Tags ------------------------------------------------------------
        $tags = [
            'bangladesh' => ['bn' => 'বাংলাদেশ', 'en' => 'Bangladesh'],
            'dhaka' => ['bn' => 'ঢাকা', 'en' => 'Dhaka'],
            'election' => ['bn' => 'নির্বাচন', 'en' => 'Election'],
            'world-cup' => ['bn' => 'বিশ্বকাপ', 'en' => 'World Cup'],
            'ai' => ['bn' => 'কৃত্রিম বুদ্ধিমত্তা', 'en' => 'AI'],
            'startup' => ['bn' => 'স্টার্টআপ', 'en' => 'Startup'],
            'budget' => ['bn' => 'বাজেট', 'en' => 'Budget'],
            'cinema' => ['bn' => 'সিনেমা', 'en' => 'Cinema'],
            'climate' => ['bn' => 'জলবায়ু', 'en' => 'Climate'],
        ];

        foreach ($tags as $slug => $name) {
            $this->tag($slug, $name);
        }
    }

    /**
     * @param  array<string,string>  $name
     * @param  array<string,string>|null  $description
     */
    protected function category(string $slug, array $name, ?int $parentId, int $position, ?string $color = null, ?string $icon = null, ?array $description = null): Category
    {
        if ($existing = Category::where('slug', $slug)->first()) {
            return $existing;
        }

        $category = Category::create([
            'parent_id' => $parentId,
            'name' => $name,
            'description' => $description,
            'color' => $color,
            'icon' => $icon,
            'position' => $position,
            'is_active' => true,
            'show_in_menu' => true,
        ]);

        $category->slug = $slug;
        $category->save();

        return $category;
    }

    /** @param array<string,string> $name */
    protected function tag(string $slug, array $name): Tag
    {
        if ($existing = Tag::where('slug', $slug)->first()) {
            return $existing;
        }

        $tag = Tag::create(['name' => $name]);
        $tag->slug = $slug;
        $tag->save();

        return $tag;
    }
}
