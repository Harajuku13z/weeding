<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('bride_name');
            $table->string('groom_name');
            $table->string('slug')->unique();
            $table->text('quote')->nullable();
            $table->text('intro_text')->nullable();
            $table->text('welcome_message')->nullable();
            $table->date('wedding_date')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('social_image')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_draft')->default(true);
            $table->boolean('envelope_animation')->default(true);
            $table->boolean('floral_decor')->default(true);
            $table->boolean('music_enabled')->default(false);
            $table->string('music_file')->nullable();
            $table->boolean('rsvp_modification_allowed')->default(true);
            $table->date('rsvp_deadline')->nullable();
            $table->string('couple_photo')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('hero_video')->nullable();
            $table->text('story_text')->nullable();
            $table->string('accommodation_info')->nullable();
            $table->text('accommodation_details')->nullable();
            $table->string('language')->default('fr');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weddings');
    }
};
