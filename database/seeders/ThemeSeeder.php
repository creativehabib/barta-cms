<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

/**
 * Registers every theme found on disk in the `themes` table and marks the
 * default "barta" theme active. Delegates activation to the ThemeManager so the
 * active_theme setting and namespace stay in sync.
 */
class ThemeSeeder extends Seeder
{
    public function run(): void
    {
        $manager = app('barta.theme');

        foreach ($manager->all() as $slug => $meta) {
            Theme::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $meta['name'] ?? $slug,
                    'version' => $meta['version'] ?? null,
                    'author' => $meta['author'] ?? null,
                    'description' => $meta['description'] ?? null,
                    'is_active' => false,
                ],
            );
        }

        // Activate the default theme (sets is_active + the active_theme setting).
        if (array_key_exists('barta', $manager->all())) {
            $manager->activate('barta');
        }
    }
}
