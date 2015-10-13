<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('config')->nullable();
            $table->mediumText('content')->nullable();
            $table->string('layout')->nullable();
            $table->string('image')->nullable()->unique();
            $table->boolean('featured')->default(0);
            $table->boolean('published')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('path')->unique();
            $table->integer('last_modified');
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
        Schema::drop('posts');
    }
}
