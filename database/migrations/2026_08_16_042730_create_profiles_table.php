<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->string('alias');
            $table->string('email')->unique();
            $table->string('password');
            $table->unsignedTinyInteger('years_experience')->default(0);
            $table->string('phone')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('github')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('profile_picture_path')->nullable();
            $table->text('short_intro_fr');
            $table->text('short_intro_en');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
