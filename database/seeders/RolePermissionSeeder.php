<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Creates the CMS permission set and the six shipped roles, wiring which role
 * gets which capabilities. Only "manage users" and "manage settings" are
 * enforced by route middleware today, but the full set is defined so the panel
 * can grow into finer-grained checks without another migration.
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Start from a clean cache so re-seeding is idempotent.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'manage posts',
            'publish posts',
            'manage categories',
            'manage tags',
            'manage comments',
            'manage media',
            'manage menus',
            'manage widgets',
            'manage themes',
            'manage plugins',
            'manage ads',
            'manage plans',
            'manage subscribers',
            'manage newsletters',
            'manage users',
            'manage settings',
        ];

        foreach ($permissions as $name) {
            Permission::findOrCreate($name, 'web');
        }

        // super-admin: everything.
        $superAdmin = Role::findOrCreate('super-admin', 'web');
        $superAdmin->syncPermissions(Permission::all());

        // admin: everything except nothing — full operational control.
        $admin = Role::findOrCreate('admin', 'web');
        $admin->syncPermissions($permissions);

        // editor: full content + moderation, no users/settings/plugins/themes.
        $editor = Role::findOrCreate('editor', 'web');
        $editor->syncPermissions([
            'manage posts', 'publish posts', 'manage categories', 'manage tags',
            'manage comments', 'manage media', 'manage widgets', 'manage ads',
            'manage newsletters', 'manage subscribers',
        ]);

        // author: create & publish their own posts, use media & tags.
        $author = Role::findOrCreate('author', 'web');
        $author->syncPermissions([
            'manage posts', 'publish posts', 'manage tags', 'manage media',
        ]);

        // contributor: draft posts only (no publish).
        $contributor = Role::findOrCreate('contributor', 'web');
        $contributor->syncPermissions([
            'manage posts', 'manage media',
        ]);

        // subscriber: a reader account, no admin capabilities.
        Role::findOrCreate('subscriber', 'web');

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
