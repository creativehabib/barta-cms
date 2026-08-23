<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeds the site's default settings. Values are intentionally sensible for a
 * fresh Bengali news portal; every one is editable from Admin → Settings.
 *
 * Note: notify_breaking_news defaults to FALSE so importing/seeding content
 * never triggers subscriber e-mail blasts.
 */
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = app('barta.settings');

        // --- General ---------------------------------------------------------
        $settings->set('site_name', 'বার্তা', 'string', 'general');
        $settings->set('site_tagline', 'সময়ের সঙ্গে', 'string', 'general');
        $settings->set('site_description', 'বার্তা — বাংলাদেশ ও বিশ্বের সর্বশেষ সংবাদ, বিশ্লেষণ ও মতামতের নির্ভরযোগ্য উৎস।', 'text', 'general');
        $settings->set('site_logo', '', 'image', 'general');
        $settings->set('active_theme', 'barta', 'string', 'general');
        $settings->set('permalink_structure', 'date', 'string', 'general');

        // --- Comments --------------------------------------------------------
        $settings->set('comments_auto_approve', false, 'bool', 'comments');

        // --- Notifications ---------------------------------------------------
        $settings->set('notify_breaking_news', false, 'bool', 'notifications');

        // --- SEO & social ----------------------------------------------------
        $settings->set('default_share_image', '', 'image', 'seo');
        $settings->set('google_analytics_id', '', 'string', 'seo');
        $settings->set('facebook_url', 'https://facebook.com/', 'string', 'social');
        $settings->set('twitter_handle', 'barta', 'string', 'social');
        $settings->set('youtube_url', 'https://youtube.com/', 'string', 'social');

        // --- Newsletter ------------------------------------------------------
        $settings->set('newsletter_pitch', 'প্রতিদিনের গুরুত্বপূর্ণ খবর সরাসরি আপনার ইনবক্সে পেতে সাবস্ক্রাইব করুন।', 'text', 'newsletter');

        $settings->flush();
    }
}
