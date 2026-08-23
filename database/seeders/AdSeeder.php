<?php

namespace Database\Seeders;

use App\Models\Ad;
use App\Models\AdSlot;
use Illuminate\Database\Seeder;

/**
 * Registers the ad slots the theme references and drops in a couple of harmless
 * HTML placeholder ads so the ad system is visible out of the box. Placeholders
 * use type = "html" (no image files needed). Delete or replace them for a real
 * campaign. Idempotent: slots are keyed; demo ads are added only once.
 */
class AdSeeder extends Seeder
{
    public function run(): void
    {
        $slots = [
            ['key' => 'header', 'name' => 'Header banner', 'width' => 728, 'height' => 90],
            ['key' => 'home-hero', 'name' => 'Home hero', 'width' => 970, 'height' => 250],
            ['key' => 'in-article', 'name' => 'In-article', 'width' => 468, 'height' => 60],
            ['key' => 'sidebar', 'name' => 'Sidebar', 'width' => 300, 'height' => 250],
            ['key' => 'footer', 'name' => 'Footer', 'width' => 728, 'height' => 90],
        ];

        $created = [];
        foreach ($slots as $slot) {
            $created[$slot['key']] = AdSlot::firstOrCreate(['key' => $slot['key']], $slot);
        }

        if (Ad::count() === 0) {
            $placeholder = fn (string $label, int $w, int $h) => '<div style="display:flex;align-items:center;justify-content:center;'
                .'min-height:'.min($h, 120).'px;background:#f3f4f6;border:1px dashed #cbd5e1;border-radius:8px;'
                .'color:#94a3b8;font-size:13px;font-weight:600;">'.$label.' ('.$w.'×'.$h.')</div>';

            Ad::create([
                'ad_slot_id' => $created['sidebar']->id,
                'name' => 'Sidebar placeholder',
                'type' => 'html',
                'content' => $placeholder('বিজ্ঞাপন / Advertisement', 300, 250),
                'is_active' => true,
            ]);

            Ad::create([
                'ad_slot_id' => $created['in-article']->id,
                'name' => 'In-article placeholder',
                'type' => 'html',
                'content' => $placeholder('বিজ্ঞাপন / Advertisement', 468, 60),
                'is_active' => true,
            ]);
        }
    }
}
