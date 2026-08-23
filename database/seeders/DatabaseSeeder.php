<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Master seeder. Order matters: roles & settings first, then accounts, taxonomy,
 * content, navigation, monetisation and finally the theme/plugin registry.
 * Every child seeder is idempotent, so `db:seed` can be re-run safely.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SettingSeeder::class,
            UserSeeder::class,
            TaxonomySeeder::class,
            PageSeeder::class,
            PostSeeder::class,
            MenuSeeder::class,
            WidgetSeeder::class,
            AdSeeder::class,
            PlanSeeder::class,
            ThemeSeeder::class,
            PluginSeeder::class,
        ]);
    }
}
