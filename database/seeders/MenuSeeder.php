<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

/**
 * Builds the primary navigation (home + top-level sections) and a footer menu of
 * static-page links. Idempotent: menus are keyed by location and items are only
 * created when the menu is empty, so re-seeding won't duplicate entries.
 */
class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $cats = Category::whereNull('parent_id')->orderBy('position')->get();

        // --- Primary navigation ---------------------------------------------
        $primary = Menu::firstOrCreate(['location' => 'primary'], ['name' => 'Primary Navigation']);

        if ($primary->items()->count() === 0) {
            MenuItem::create([
                'menu_id' => $primary->id,
                'label' => ['bn' => 'হোম', 'en' => 'Home'],
                'type' => 'custom',
                'url' => '/',
                'position' => 0,
            ]);

            foreach ($cats as $i => $cat) {
                MenuItem::create([
                    'menu_id' => $primary->id,
                    'label' => $cat->getTranslations('name'),
                    'type' => 'category',
                    'target_id' => $cat->id,
                    'position' => $i + 1,
                ]);
            }
        }

        // --- Footer menu -----------------------------------------------------
        $footer = Menu::firstOrCreate(['location' => 'footer'], ['name' => 'Footer Links']);

        if ($footer->items()->count() === 0) {
            $links = [
                ['label' => ['bn' => 'আমাদের সম্পর্কে', 'en' => 'About us'], 'url' => '/page/about'],
                ['label' => ['bn' => 'যোগাযোগ', 'en' => 'Contact'], 'url' => '/page/contact'],
                ['label' => ['bn' => 'গোপনীয়তা নীতি', 'en' => 'Privacy'], 'url' => '/page/privacy'],
                ['label' => ['bn' => 'ব্যবহারের শর্তাবলি', 'en' => 'Terms'], 'url' => '/page/terms'],
            ];

            foreach ($links as $i => $link) {
                MenuItem::create([
                    'menu_id' => $footer->id,
                    'label' => $link['label'],
                    'type' => 'custom',
                    'url' => $link['url'],
                    'position' => $i,
                ]);
            }
        }
    }
}
