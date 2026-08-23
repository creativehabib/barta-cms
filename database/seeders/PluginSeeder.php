<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Discovers plugins on disk and registers them in the `plugins` table via the
 * PluginManager. Plugins are left INACTIVE by default — activate the bundled
 * "Reading Progress" plugin from Admin → Plugins when you want it live.
 */
class PluginSeeder extends Seeder
{
    public function run(): void
    {
        app('barta.plugin')->sync();
    }
}
