<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('content_type_id')->nullable();
            $table->string('title');
            $table->string('slug');
            $table->mediumText('body')->nullable();
            $table->string('layout')->nullable();
            $table->boolean('featured')->default(0);
            $table->boolean('published')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->string('meta_title', 55)->nullable()->unique();
            $table->string('meta_description', 155)->nullable();
            $table->string('path')->unique();
            $table->integer('last_modified');
            $table->string('config')->nullable();
            $table->unique(['content_type_id', 'slug']);
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
        Schema::drop('contents');
    }
}
