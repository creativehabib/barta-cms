<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();

            $table->json('title');                 // translatable
            $table->string('slug')->unique();
            $table->json('excerpt')->nullable();   // translatable
            $table->json('body')->nullable();      // translatable (HTML)

            $table->string('type')->default('post');       // post | page
            $table->string('status')->default('draft');    // draft | pending | published | scheduled | archived
            $table->string('format')->default('standard'); // standard | video | gallery | audio

            $table->boolean('is_premium')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_breaking')->default(false);
            $table->boolean('allow_comments')->default(true);

            $table->string('source')->nullable();
            $table->string('source_url')->nullable();
            $table->string('video_url')->nullable();

            $table->unsignedBigInteger('views')->default(0)->index();

            $table->json('meta_title')->nullable();        // SEO, translatable
            $table->json('meta_description')->nullable();  // SEO, translatable

            $table->timestamp('published_at')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
