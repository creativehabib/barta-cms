<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds the initial staff accounts and one reader. All demo accounts use the
 * password "password" — change or remove them before going live.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@barta.test'],
            [
                'name' => 'সম্পাদক',
                'username' => 'admin',
                'password' => 'password',
                'bio' => 'বার্তার প্রধান সম্পাদক।',
                'locale' => 'bn',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );
        $admin->syncRoles('super-admin');

        $editor = User::updateOrCreate(
            ['email' => 'editor@barta.test'],
            [
                'name' => 'রফিকুল ইসলাম',
                'username' => 'rafiqul',
                'password' => 'password',
                'bio' => 'বার্তার বার্তা সম্পাদক। জাতীয় ও আন্তর্জাতিক রাজনীতি নিয়ে লেখেন।',
                'locale' => 'bn',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );
        $editor->syncRoles('editor');

        $authors = [
            ['email' => 'tanvir@barta.test', 'name' => 'তানভীর আহমেদ', 'username' => 'tanvir', 'bio' => 'ক্রীড়া প্রতিবেদক।'],
            ['email' => 'nusrat@barta.test', 'name' => 'নুসরাত জাহান', 'username' => 'nusrat', 'bio' => 'প্রযুক্তি ও বিজ্ঞান বিষয়ক লেখক।'],
            ['email' => 'shakil@barta.test', 'name' => 'শাকিল মাহমুদ', 'username' => 'shakil', 'bio' => 'অর্থনীতি ও ব্যবসা বিভাগের প্রতিবেদক।'],
        ];

        foreach ($authors as $data) {
            $author = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'password' => 'password',
                    'bio' => $data['bio'],
                    'locale' => 'bn',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ],
            );
            $author->syncRoles('author');
        }

        $reader = User::updateOrCreate(
            ['email' => 'reader@barta.test'],
            [
                'name' => 'পাঠক',
                'username' => 'reader',
                'password' => 'password',
                'locale' => 'bn',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );
        $reader->syncRoles('subscriber');
    }
}
