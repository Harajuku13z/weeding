<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wedding_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('invitation_code')->unique();
            $table->string('rsvp_status')->default('pending'); // pending, accepted, declined, maybe
            $table->integer('companions_count')->default(0);
            $table->boolean('companions_allowed')->default(false);
            $table->integer('max_companions')->default(0);
            $table->text('dietary_restrictions')->nullable();
            $table->text('notes_admin')->nullable();
            $table->json('tags')->nullable();
            $table->string('contact_channel')->default('email'); // email, sms, whatsapp
            $table->boolean('is_suspended')->default(false);
            $table->string('personal_message')->nullable();
            $table->timestamp('rsvp_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
