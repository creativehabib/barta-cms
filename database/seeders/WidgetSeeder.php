<?php

namespace Database\Seeders;

use App\Models\Widget;
use Illuminate\Database\Seeder;

/**
 * Populates the theme's widget areas (sidebar, home-bottom, footer-1..3) with a
 * sensible default arrangement. Idempotent: only runs when no widgets exist yet.
 */
class WidgetSeeder extends Seeder
{
    public function run(): void
    {
        if (Widget::count() > 0) {
            return;
        }

        $widgets = [
            // Sidebar (used on single/category/etc.)
            ['area' => 'sidebar', 'type' => 'popular_posts', 'title' => ['bn' => 'সর্বাধিক পঠিত', 'en' => 'Most read'], 'settings' => ['count' => 5], 'position' => 0],
            ['area' => 'sidebar', 'type' => 'category_list', 'title' => ['bn' => 'বিভাগসমূহ', 'en' => 'Sections'], 'settings' => null, 'position' => 1],
            ['area' => 'sidebar', 'type' => 'newsletter', 'title' => ['bn' => 'নিউজলেটার', 'en' => 'Newsletter'], 'settings' => null, 'position' => 2],
            ['area' => 'sidebar', 'type' => 'ad', 'title' => null, 'settings' => ['slot' => 'sidebar'], 'position' => 3],

            // Home bottom (full-width strip near the footer)
            ['area' => 'home-bottom', 'type' => 'tag_cloud', 'title' => ['bn' => 'জনপ্রিয় ট্যাগ', 'en' => 'Popular tags'], 'settings' => ['count' => 15], 'position' => 0],

            // Footer columns
            ['area' => 'footer-1', 'type' => 'recent_posts', 'title' => ['bn' => 'সাম্প্রতিক খবর', 'en' => 'Recent news'], 'settings' => ['count' => 4], 'position' => 0],
            ['area' => 'footer-2', 'type' => 'category_list', 'title' => ['bn' => 'বিভাগসমূহ', 'en' => 'Sections'], 'settings' => null, 'position' => 0],
            ['area' => 'footer-3', 'type' => 'html', 'title' => ['bn' => 'বার্তা সম্পর্কে', 'en' => 'About Barta'], 'settings' => ['html' => '<p>বার্তা — বাংলাদেশ ও বিশ্বের নির্ভরযোগ্য সংবাদমাধ্যম। সত্য, নিরপেক্ষতা ও দায়বদ্ধতাই আমাদের মূল্যবোধ।</p>'], 'position' => 0],
        ];

        foreach ($widgets as $w) {
            Widget::create([
                'area' => $w['area'],
                'type' => $w['type'],
                'title' => $w['title'],
                'settings' => $w['settings'],
                'position' => $w['position'],
                'is_active' => true,
            ]);
        }
    }
}
