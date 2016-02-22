<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('column_content', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('column_id')->unsigned()->index();
            $table->foreign('column_id')->references('id')->on('columns')->onDelete('cascade');
            $table->integer('content_id')->unsigned()->index();
            $table->foreign('content_id')->references('id')->on('contents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('column_content');
    }
}
