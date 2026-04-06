<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wedding_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wedding_id')->constrained()->onDelete('cascade');
            $table->string('color_primary')->default('#c8a97e');
            $table->string('color_secondary')->default('#f5e6d3');
            $table->string('color_accent')->default('#8b6355');
            $table->string('color_background')->default('#fdfaf7');
            $table->string('color_text')->default('#3d2b1f');
            $table->string('font_title')->default('Playfair Display');
            $table->string('font_body')->default('Lato');
            $table->string('button_style')->default('rounded');
            $table->string('border_radius')->default('8px');
            $table->string('shadow_intensity')->default('medium');
            $table->string('animation_intensity')->default('medium');
            $table->string('dress_code_style')->nullable();
            $table->string('dress_code_formality')->nullable();
            $table->text('dress_code_description')->nullable();
            $table->text('dress_code_men')->nullable();
            $table->text('dress_code_women')->nullable();
            $table->text('dress_code_accessories')->nullable();
            $table->string('forbidden_colors')->nullable();
            $table->text('mood_description')->nullable();
            $table->string('mood_textures')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wedding_themes');
    }
};
