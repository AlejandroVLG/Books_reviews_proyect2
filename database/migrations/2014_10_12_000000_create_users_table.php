<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

            $table->id();
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('nick_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('gender')->nullable();
            $table->integer('age')->nullable();
            $table->string('country')->nullable();
            $table->string('favourite_author')->nullable();
            $table->string('favourite_genre')->nullable();
            $table->string('currently_reading')->nullable();
            $table->string('facebook_account')->unique()->nullable();
            $table->string('twitter_account')->unique()->nullable();
            $table->string('instagram_account')->unique()->nullable();
            $table->string('profile_img')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
