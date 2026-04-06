<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wedding_program_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wedding_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->string('venue_name')->nullable();
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->default('bi-star');
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wedding_program_items');
    }
};
