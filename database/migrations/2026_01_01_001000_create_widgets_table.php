<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->string('area')->index();  // sidebar | home_top | home_bottom | footer_1 ...
            $table->string('type');           // recent_posts | popular_posts | category_list | html | ads | newsletter | tags
            $table->json('title')->nullable(); // translatable
            $table->json('settings')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('widgets');
    }
};
