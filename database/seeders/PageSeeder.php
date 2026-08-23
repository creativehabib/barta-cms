<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds the standard static pages (type = "page") that the footer menu links to.
 * Slugs are forced to clean ASCII values, mirroring the post/category approach.
 */
class PageSeeder extends Seeder
{
    public function run(): void
    {
        $authorId = User::where('email', 'admin@barta.test')->value('id') ?? User::query()->value('id');

        $pages = [
            [
                'slug' => 'about',
                'title' => ['bn' => 'আমাদের সম্পর্কে', 'en' => 'About us'],
                'body' => [
                    'bn' => '<p>বার্তা একটি স্বাধীন সংবাদমাধ্যম, যা বাংলাদেশ ও বিশ্বের নির্ভরযোগ্য খবর, বিশ্লেষণ ও মতামত পাঠকের কাছে পৌঁছে দিতে প্রতিশ্রুতিবদ্ধ।</p><p>সত্য, নিরপেক্ষতা ও দায়বদ্ধতা—এই তিন মূল্যবোধকে ধারণ করে আমরা সাংবাদিকতা করি।</p>',
                    'en' => '<p>Barta is an independent news outlet committed to delivering reliable news, analysis and opinion from Bangladesh and the world.</p><p>We practise journalism grounded in truth, fairness and accountability.</p>',
                ],
            ],
            [
                'slug' => 'contact',
                'title' => ['bn' => 'যোগাযোগ', 'en' => 'Contact'],
                'body' => [
                    'bn' => '<p>সংবাদ, বিজ্ঞাপন বা যেকোনো জিজ্ঞাসায় আমাদের সঙ্গে যোগাযোগ করুন।</p><p>ইমেইল: news@barta.test<br>ঠিকানা: ঢাকা, বাংলাদেশ।</p>',
                    'en' => '<p>Get in touch with us for news tips, advertising or any inquiry.</p><p>Email: news@barta.test<br>Address: Dhaka, Bangladesh.</p>',
                ],
            ],
            [
                'slug' => 'privacy',
                'title' => ['bn' => 'গোপনীয়তা নীতি', 'en' => 'Privacy policy'],
                'body' => [
                    'bn' => '<p>আমরা পাঠকের ব্যক্তিগত তথ্যের গোপনীয়তা রক্ষায় অঙ্গীকারবদ্ধ। এই নীতিতে বর্ণিত হয়েছে কীভাবে আমরা তথ্য সংগ্রহ ও ব্যবহার করি।</p><p>এটি একটি নমুনা নীতি; প্রকৃত ব্যবহারের আগে হালনাগাদ করুন।</p>',
                    'en' => '<p>We are committed to protecting the privacy of our readers. This policy describes how we collect and use information.</p><p>This is placeholder text; update it before going live.</p>',
                ],
            ],
            [
                'slug' => 'terms',
                'title' => ['bn' => 'ব্যবহারের শর্তাবলি', 'en' => 'Terms of use'],
                'body' => [
                    'bn' => '<p>এই ওয়েবসাইট ব্যবহারের মাধ্যমে আপনি নিম্নলিখিত শর্তাবলি মেনে নিচ্ছেন।</p><p>এটি একটি নমুনা পাঠ্য; প্রকৃত ব্যবহারের আগে হালনাগাদ করুন।</p>',
                    'en' => '<p>By using this website you agree to the following terms and conditions.</p><p>This is placeholder text; update it before going live.</p>',
                ],
            ],
        ];

        foreach ($pages as $data) {
            if (Post::where('slug', $data['slug'])->exists()) {
                continue;
            }

            $page = Post::create([
                'user_id' => $authorId,
                'title' => $data['title'],
                'body' => $data['body'],
                'type' => 'page',
                'status' => 'published',
                'allow_comments' => false,
                'published_at' => now()->subMonth(),
            ]);

            $page->slug = $data['slug'];
            $page->save();
        }
    }
}
