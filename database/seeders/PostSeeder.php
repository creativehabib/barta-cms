<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Seeds bilingual (bn/en) demo articles across the sections created by the
 * TaxonomySeeder, plus a handful of approved comments. Posts get clean forced
 * slugs and are spread over the last few weeks so archives and "latest" feeds
 * look natural. No media is attached — the theme renders coloured placeholders
 * for cover-less posts, keeping `db:seed` free of image-toolchain requirements.
 */
class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::pluck('id', 'email');
        $cats = Category::pluck('id', 'slug');
        $tags = Tag::pluck('id', 'slug');

        foreach ($this->posts() as $def) {
            if (Post::where('slug', $def['slug'])->exists()) {
                continue;
            }

            $post = Post::create([
                'user_id' => $users[$def['author']] ?? $users->first(),
                'category_id' => $cats[$def['category']] ?? null,
                'title' => $def['title'],
                'excerpt' => $def['excerpt'],
                'body' => $def['body'],
                'type' => 'post',
                'status' => 'published',
                'format' => 'standard',
                'is_premium' => $def['premium'] ?? false,
                'is_featured' => $def['featured'] ?? false,
                'is_breaking' => $def['breaking'] ?? false,
                'allow_comments' => true,
                'views' => $def['views'] ?? random_int(120, 5000),
                'published_at' => Carbon::now()->subDays($def['days'])->setTime(9 + ($def['days'] % 8), ($def['days'] * 7) % 60),
            ]);

            $post->slug = $def['slug'];
            $post->save();

            $tagIds = array_values(array_filter(array_map(fn ($t) => $tags[$t] ?? null, $def['tags'] ?? [])));
            if ($tagIds) {
                $post->tags()->sync($tagIds);
            }

            foreach ($def['comments'] ?? [] as $c) {
                Comment::create([
                    'post_id' => $post->id,
                    'user_id' => isset($c['user']) ? ($users[$c['user']] ?? null) : null,
                    'author_name' => $c['name'] ?? null,
                    'author_email' => $c['email'] ?? null,
                    'body' => $c['body'],
                    'status' => 'approved',
                    'ip_address' => '127.0.0.1',
                    'created_at' => $post->published_at->copy()->addHours(random_int(1, 40)),
                ]);
            }
        }
    }

    /** @return array<int, array<string, mixed>> */
    protected function posts(): array
    {
        $body = fn (array $bn, array $en) => [
            'bn' => collect($bn)->map(fn ($x) => '<p>'.$x.'</p>')->implode("\n"),
            'en' => collect($en)->map(fn ($x) => '<p>'.$x.'</p>')->implode("\n"),
        ];

        return [
            [
                'slug' => 'national-budget-2026-27',
                'category' => 'economy',
                'author' => 'shakil@barta.test',
                'tags' => ['budget', 'bangladesh'],
                'featured' => true,
                'days' => 1,
                'views' => 8420,
                'title' => ['bn' => '২০২৬-২৭ অর্থবছরের বাজেট: বরাদ্দ বাড়ল শিক্ষা ও স্বাস্থ্য খাতে', 'en' => '2026-27 Budget: Bigger allocations for education and health'],
                'excerpt' => ['bn' => 'নতুন অর্থবছরের প্রস্তাবিত বাজেটে সামাজিক খাতে বরাদ্দ উল্লেখযোগ্য হারে বাড়ানো হয়েছে।', 'en' => 'The proposed budget raises social-sector spending significantly.'],
                'body' => $body(
                    ['জাতীয় সংসদে উত্থাপিত ২০২৬-২৭ অর্থবছরের প্রস্তাবিত বাজেটে শিক্ষা ও স্বাস্থ্য খাতে বরাদ্দ গত বছরের তুলনায় বাড়ানো হয়েছে। অর্থমন্ত্রী বলেন, মানবসম্পদ উন্নয়নই এবারের বাজেটের মূল অগ্রাধিকার।', 'বাজেটে মূল্যস্ফীতি নিয়ন্ত্রণ, রাজস্ব আদায় বৃদ্ধি এবং বেসরকারি বিনিয়োগ উৎসাহিত করার ওপর গুরুত্ব দেওয়া হয়েছে। বিশ্লেষকেরা বলছেন, বাস্তবায়নই হবে বড় চ্যালেঞ্জ।'],
                    ['The proposed 2026-27 budget placed before parliament raises allocations for education and health compared with last year. The finance minister said human-capital development is the central priority this year.', 'The budget emphasises taming inflation, boosting revenue collection and encouraging private investment. Analysts say execution will be the real challenge.']
                ),
                'comments' => [
                    ['user' => 'reader@barta.test', 'body' => 'শিক্ষায় বরাদ্দ বাড়ানোটা ইতিবাচক খবর।'],
                    ['name' => 'কামরুল হাসান', 'email' => 'kamrul@example.com', 'body' => 'বাস্তবায়ন ঠিকমতো হলে ভালো।'],
                ],
            ],
            [
                'slug' => 'dhaka-metro-rail-new-line',
                'category' => 'national',
                'author' => 'editor@barta.test',
                'tags' => ['dhaka', 'bangladesh'],
                'featured' => true,
                'days' => 2,
                'views' => 6130,
                'title' => ['bn' => 'ঢাকা মেট্রোরেলের নতুন লাইন চালু, কমবে যানজট', 'en' => 'New Dhaka metro line opens, easing traffic'],
                'excerpt' => ['bn' => 'রাজধানীর যানজট নিরসনে মেট্রোরেলের নতুন একটি লাইন যাত্রীদের জন্য উন্মুক্ত করা হয়েছে।', 'en' => 'A new metro line has opened to passengers to help ease the capital\'s congestion.'],
                'body' => $body(
                    ['রাজধানী ঢাকার যানজট কমাতে মেট্রোরেলের নতুন একটি লাইন আজ আনুষ্ঠানিকভাবে যাত্রীদের জন্য খুলে দেওয়া হয়েছে। প্রথম দিনেই লাখো যাত্রী নতুন এই লাইন ব্যবহার করেছেন।', 'কর্তৃপক্ষ জানিয়েছে, পর্যায়ক্রমে আরও কয়েকটি লাইন যুক্ত হবে, যা নগরবাসীর যাতায়াত সহজ করবে।'],
                    ['A new metro line opened to passengers today to help cut congestion in the capital. Hundreds of thousands rode the line on its first day.', 'Authorities said more lines will be added in phases, making commuting easier for residents.']
                ),
                'comments' => [
                    ['name' => 'সাদিয়া', 'email' => 'sadia@example.com', 'body' => 'অফিসে যাওয়া এখন অনেক সহজ হবে।'],
                ],
            ],
            [
                'slug' => 'bangladesh-cricket-series-win',
                'category' => 'cricket',
                'author' => 'tanvir@barta.test',
                'tags' => ['world-cup', 'bangladesh'],
                'breaking' => true,
                'days' => 0,
                'views' => 15230,
                'title' => ['bn' => 'শ্বাসরুদ্ধকর ম্যাচে সিরিজ জিতল বাংলাদেশ', 'en' => 'Bangladesh clinch series in a thriller'],
                'excerpt' => ['bn' => 'শেষ ওভারের নাটকীয়তায় প্রতিপক্ষকে হারিয়ে সিরিজ নিশ্চিত করেছে টাইগাররা।', 'en' => 'A last-over thriller sealed the series for the Tigers.'],
                'body' => $body(
                    ['শেষ ওভারের রোমাঞ্চকর লড়াইয়ে প্রতিপক্ষকে হারিয়ে সিরিজ জিতে নিয়েছে বাংলাদেশ। অধিনায়কের দায়িত্বশীল ইনিংস দলকে জয়ের বন্দরে পৌঁছে দেয়।', 'ম্যাচসেরা হয়েছেন তরুণ অলরাউন্ডার। সমর্থকেরা রাতভর উৎসবে মেতে ওঠেন।'],
                    ['Bangladesh won the series after a nail-biting final over. A responsible captain\'s knock carried the side home.', 'A young all-rounder was named player of the match as fans celebrated late into the night.']
                ),
                'comments' => [
                    ['user' => 'reader@barta.test', 'body' => 'অসাধারণ জয়! অভিনন্দন টাইগারদের।'],
                    ['name' => 'রাকিব', 'email' => 'rakib@example.com', 'body' => 'শেষ ওভারটা দেখে হার্ট অ্যাটাক হওয়ার জোগাড়!'],
                ],
            ],
            [
                'slug' => 'global-climate-summit-2026',
                'category' => 'international',
                'author' => 'editor@barta.test',
                'tags' => ['climate'],
                'days' => 3,
                'views' => 3410,
                'title' => ['bn' => 'জলবায়ু সম্মেলনে নতুন অঙ্গীকার বিশ্বনেতাদের', 'en' => 'World leaders make fresh pledges at climate summit'],
                'excerpt' => ['bn' => 'কার্বন নিঃসরণ কমাতে নতুন প্রতিশ্রুতি দিয়েছেন বিশ্বের নেতারা।', 'en' => 'Leaders pledged fresh cuts to carbon emissions.'],
                'body' => $body(
                    ['বৈশ্বিক জলবায়ু সম্মেলনে কার্বন নিঃসরণ উল্লেখযোগ্য হারে কমানোর অঙ্গীকার করেছেন বিশ্বনেতারা। উন্নয়নশীল দেশগুলোর জন্য জলবায়ু তহবিল বাড়ানোর ঘোষণাও এসেছে।', 'বাংলাদেশসহ ঝুঁকিপূর্ণ দেশগুলো ন্যায্য অর্থায়নের দাবি জানিয়েছে।'],
                    ['World leaders pledged to sharply cut carbon emissions at the global climate summit, alongside a promise to expand climate funds for developing nations.', 'Vulnerable countries including Bangladesh pressed for fair financing.']
                ),
            ],
            [
                'slug' => 'ai-powering-bangladeshi-startups',
                'category' => 'technology',
                'author' => 'nusrat@barta.test',
                'tags' => ['ai', 'startup'],
                'premium' => true,
                'days' => 4,
                'views' => 2870,
                'title' => ['bn' => 'কৃত্রিম বুদ্ধিমত্তায় বদলে যাচ্ছে দেশের স্টার্টআপ', 'en' => 'How AI is reshaping Bangladesh\'s startups'],
                'excerpt' => ['bn' => 'কৃষি থেকে স্বাস্থ্য—নানা খাতে এআই ব্যবহার করছে দেশের নতুন উদ্যোগগুলো।', 'en' => 'From agriculture to health, local startups are adopting AI.'],
                'body' => $body(
                    ['দেশের তরুণ উদ্যোক্তারা কৃত্রিম বুদ্ধিমত্তা কাজে লাগিয়ে কৃষি, স্বাস্থ্য ও আর্থিক সেবায় নতুন সমাধান আনছেন। বিনিয়োগকারীদের আগ্রহও বাড়ছে।', 'বিশেষজ্ঞরা বলছেন, দক্ষ জনবল ও সঠিক নীতিসহায়তা পেলে এই খাত বিশ্ববাজারে প্রতিযোগিতা করতে পারবে।'],
                    ['Young entrepreneurs are using AI to build new solutions in agriculture, health and finance, drawing rising investor interest.', 'Experts say the sector could compete globally with skilled talent and the right policy support.']
                ),
            ],
            [
                'slug' => 'taka-dollar-exchange-update',
                'category' => 'economy',
                'author' => 'shakil@barta.test',
                'tags' => ['budget'],
                'days' => 5,
                'views' => 1990,
                'title' => ['bn' => 'ডলারের বিপরীতে স্থিতিশীল টাকা, স্বস্তি আমদানিকারকদের', 'en' => 'Taka steadies against the dollar, relief for importers'],
                'excerpt' => ['bn' => 'কয়েক সপ্তাহের অস্থিরতার পর বৈদেশিক মুদ্রাবাজারে স্থিতিশীলতা ফিরেছে।', 'en' => 'The currency market has stabilised after weeks of volatility.'],
                'body' => $body(
                    ['কয়েক সপ্তাহের অস্থিরতার পর ডলারের বিপরীতে টাকার বিনিময় হার তুলনামূলক স্থিতিশীল হয়েছে। কেন্দ্রীয় ব্যাংকের পদক্ষেপ এতে ভূমিকা রেখেছে বলে মনে করছেন বিশ্লেষকেরা।', 'আমদানিকারকেরা বলছেন, স্থিতিশীলতা বজায় থাকলে পণ্যমূল্যে এর ইতিবাচক প্রভাব পড়বে।'],
                    ['After weeks of volatility, the taka has steadied against the dollar, helped by central-bank measures, analysts say.', 'Importers say sustained stability would ease pressure on prices.']
                ),
            ],
            [
                'slug' => 'dhaka-international-film-festival',
                'category' => 'entertainment',
                'author' => 'nusrat@barta.test',
                'tags' => ['cinema', 'dhaka'],
                'days' => 6,
                'views' => 2450,
                'title' => ['bn' => 'ঢাকা আন্তর্জাতিক চলচ্চিত্র উৎসব শুরু', 'en' => 'Dhaka International Film Festival begins'],
                'excerpt' => ['bn' => 'দেশ-বিদেশের শতাধিক চলচ্চিত্র নিয়ে শুরু হয়েছে এবারের আয়োজন।', 'en' => 'This year\'s edition opens with over a hundred films.'],
                'body' => $body(
                    ['নানা দেশের শতাধিক চলচ্চিত্র নিয়ে রাজধানীতে শুরু হয়েছে ঢাকা আন্তর্জাতিক চলচ্চিত্র উৎসব। উদ্বোধনী দিনে দর্শকদের ভিড় ছিল লক্ষণীয়।', 'আয়োজকেরা জানিয়েছেন, তরুণ নির্মাতাদের জন্য থাকছে বিশেষ কর্মশালা।'],
                    ['The Dhaka International Film Festival opened in the capital with more than a hundred films from many countries, drawing large opening-day crowds.', 'Organisers said special workshops for young filmmakers are on the schedule.']
                ),
            ],
            [
                'slug' => 'premier-league-final-dhaka',
                'category' => 'football',
                'author' => 'tanvir@barta.test',
                'tags' => ['world-cup'],
                'days' => 8,
                'views' => 1760,
                'title' => ['bn' => 'ঘরোয়া ফুটবল লিগের ফাইনাল আজ', 'en' => 'Domestic football league final today'],
                'excerpt' => ['bn' => 'শিরোপা নির্ধারণী ম্যাচ ঘিরে সমর্থকদের মধ্যে উত্তেজনা তুঙ্গে।', 'en' => 'Fans are buzzing ahead of the title decider.'],
                'body' => $body(
                    ['ঘরোয়া ফুটবল লিগের শিরোপা নির্ধারণী ম্যাচ আজ অনুষ্ঠিত হবে। দুই দলই সেরা একাদশ নিয়ে মাঠে নামার প্রস্তুতি নিয়েছে।', 'কোচরা বলছেন, মাঝমাঠের নিয়ন্ত্রণই গড়ে দেবে ম্যাচের ভাগ্য।'],
                    ['The domestic football league title decider takes place today, with both sides set to field their strongest elevens.', 'Coaches say control of midfield will settle the match.']
                ),
            ],
            [
                'slug' => 'opinion-reforming-our-education',
                'category' => 'opinion',
                'author' => 'editor@barta.test',
                'tags' => ['bangladesh'],
                'days' => 9,
                'views' => 1320,
                'title' => ['bn' => 'মতামত: শিক্ষার সংস্কার কেন এখনই জরুরি', 'en' => 'Opinion: Why education reform can\'t wait'],
                'excerpt' => ['bn' => 'মুখস্থনির্ভর শিক্ষা থেকে বেরিয়ে দক্ষতাভিত্তিক পাঠক্রমে যাওয়ার সময় এসেছে।', 'en' => 'It is time to move from rote learning to skills-based curricula.'],
                'body' => $body(
                    ['আমাদের শিক্ষাব্যবস্থা এখনো অনেকাংশে মুখস্থনির্ভর। দ্রুত বদলে যাওয়া কর্মবাজারের সঙ্গে তাল মেলাতে দরকার দক্ষতাভিত্তিক পাঠক্রম।', 'শিক্ষক প্রশিক্ষণ ও প্রযুক্তির সমন্বয় ছাড়া কেবল অবকাঠামো দিয়ে এই লক্ষ্য অর্জন সম্ভব নয়।'],
                    ['Our education system still leans heavily on rote learning. Keeping pace with a fast-changing job market needs skills-based curricula.', 'Infrastructure alone won\'t deliver this without teacher training and technology.']
                ),
            ],
            [
                'slug' => 'south-asia-regional-trade',
                'category' => 'south-asia',
                'author' => 'shakil@barta.test',
                'tags' => ['bangladesh'],
                'days' => 11,
                'views' => 980,
                'title' => ['bn' => 'আঞ্চলিক বাণিজ্য বাড়াতে নতুন উদ্যোগ', 'en' => 'New push to expand regional trade'],
                'excerpt' => ['bn' => 'দক্ষিণ এশিয়ার দেশগুলোর মধ্যে বাণিজ্য সহজ করতে আলোচনা এগিয়েছে।', 'en' => 'Talks advance on easing trade among South Asian nations.'],
                'body' => $body(
                    ['দক্ষিণ এশিয়ার দেশগুলোর মধ্যে পণ্য ও সেবা বাণিজ্য সহজ করতে নতুন উদ্যোগের কথা জানিয়েছেন নীতিনির্ধারকেরা।', 'বিশেষজ্ঞরা বলছেন, অবকাঠামো ও শুল্কনীতির সমন্বয় হলে আঞ্চলিক বাণিজ্য কয়েক গুণ বাড়তে পারে।'],
                    ['Policymakers announced fresh initiatives to ease trade in goods and services among South Asian countries.', 'Experts say aligning infrastructure and tariffs could multiply regional trade.']
                ),
            ],
            [
                'slug' => 'flagship-smartphone-review',
                'category' => 'technology',
                'author' => 'nusrat@barta.test',
                'tags' => ['ai'],
                'days' => 13,
                'views' => 3120,
                'title' => ['bn' => 'নতুন ফ্ল্যাগশিপ স্মার্টফোন: যা কিছু নতুন', 'en' => 'New flagship smartphone: what\'s new'],
                'excerpt' => ['bn' => 'উন্নত ক্যামেরা ও এআই ফিচার নিয়ে বাজারে এসেছে নতুন ফ্ল্যাগশিপ।', 'en' => 'A new flagship arrives with a better camera and AI features.'],
                'body' => $body(
                    ['উন্নত ক্যামেরা, দ্রুততর প্রসেসর ও নতুন এআই ফিচার নিয়ে বাজারে এসেছে সর্বশেষ ফ্ল্যাগশিপ স্মার্টফোনটি। ব্যাটারি ব্যাকআপেও উন্নতি এসেছে।', 'দাম কিছুটা বেশি হলেও সামগ্রিক অভিজ্ঞতা প্রিমিয়াম, বলছেন পর্যালোচকেরা।'],
                    ['The latest flagship arrives with a better camera, faster processor and new AI features, along with improved battery life.', 'The price is a touch high, but the overall experience is premium, reviewers say.']
                ),
            ],
            [
                'slug' => 'election-commission-preparations',
                'category' => 'politics',
                'author' => 'editor@barta.test',
                'tags' => ['election', 'bangladesh'],
                'featured' => true,
                'days' => 15,
                'views' => 5240,
                'title' => ['bn' => 'নির্বাচনী প্রস্তুতি জোরদার করছে কমিশন', 'en' => 'Commission steps up election preparations'],
                'excerpt' => ['bn' => 'ভোটার তালিকা হালনাগাদসহ নানা প্রস্তুতি এগিয়ে নিচ্ছে নির্বাচন কমিশন।', 'en' => 'The commission advances voter-roll updates and other preparations.'],
                'body' => $body(
                    ['আসন্ন নির্বাচনকে সামনে রেখে ভোটার তালিকা হালনাগাদসহ নানা প্রস্তুতি জোরদার করছে নির্বাচন কমিশন। সুষ্ঠু ভোট আয়োজনে সব ধরনের সহযোগিতার আশ্বাস দিয়েছে সংস্থাটি।', 'রাজনৈতিক দলগুলোর সঙ্গে ধারাবাহিক সংলাপের কথাও জানিয়েছে কমিশন।'],
                    ['Ahead of the coming election, the commission is stepping up preparations including voter-roll updates, pledging cooperation for a fair vote.', 'It also said it will hold continued dialogue with political parties.']
                ),
            ],
            [
                'slug' => 'premium-deep-dive-economy',
                'category' => 'economy',
                'author' => 'shakil@barta.test',
                'tags' => ['budget', 'bangladesh'],
                'premium' => true,
                'days' => 18,
                'views' => 1450,
                'title' => ['bn' => 'বিশেষ বিশ্লেষণ: প্রবৃদ্ধির পেছনের গল্প', 'en' => 'Deep dive: the story behind the growth numbers'],
                'excerpt' => ['bn' => 'অর্থনৈতিক প্রবৃদ্ধির পরিসংখ্যানের আড়ালে থাকা বাস্তবতা নিয়ে বিশ্লেষণ।', 'en' => 'An analysis of what lies behind the growth statistics.'],
                'body' => $body(
                    ['অর্থনৈতিক প্রবৃদ্ধির পরিসংখ্যান আশাব্যঞ্জক হলেও এর সুফল কতটা সাধারণ মানুষের কাছে পৌঁছেছে, তা নিয়ে প্রশ্ন রয়েছে। এই বিশেষ বিশ্লেষণে আমরা তথ্য-উপাত্তের গভীরে গেছি।', 'কর্মসংস্থান, আয় বৈষম্য ও বেসরকারি বিনিয়োগের প্রবণতা বিশ্লেষণ করে ভবিষ্যৎ দিকনির্দেশনা তুলে ধরা হয়েছে।'],
                    ['Growth figures look encouraging, but questions remain about how far the gains have reached ordinary people. This deep dive digs into the data.', 'We analyse jobs, income inequality and private-investment trends to sketch what comes next.']
                ),
            ],
            [
                'slug' => 'youth-cricket-academy-launch',
                'category' => 'cricket',
                'author' => 'tanvir@barta.test',
                'tags' => ['bangladesh', 'world-cup'],
                'days' => 21,
                'views' => 870,
                'title' => ['bn' => 'তরুণ ক্রিকেটারদের জন্য নতুন একাডেমি', 'en' => 'New academy to nurture young cricketers'],
                'excerpt' => ['bn' => 'প্রতিভাবান তরুণদের গড়ে তুলতে চালু হলো আধুনিক ক্রিকেট একাডেমি।', 'en' => 'A modern cricket academy opens to develop young talent.'],
                'body' => $body(
                    ['প্রতিভাবান তরুণ ক্রিকেটারদের গড়ে তুলতে আধুনিক সুযোগ-সুবিধাসম্পন্ন একটি একাডেমি চালু হয়েছে। থাকছে অভিজ্ঞ কোচিং প্যানেল।', 'একাডেমি কর্তৃপক্ষ জানিয়েছে, তৃণমূল থেকে খেলোয়াড় তুলে আনাই তাদের লক্ষ্য।'],
                    ['A modern academy with strong facilities has opened to develop talented young cricketers, backed by an experienced coaching panel.', 'Officials said their goal is to bring players up from the grassroots.']
                ),
            ],
        ];
    }
}
