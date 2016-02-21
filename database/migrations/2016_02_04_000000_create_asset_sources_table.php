<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_sources', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('sequence')->default(0);
            $table->string('mimetype');
            $table->string('uri')->unique();
            $table->string('title')->nullable();
            $table->string('alt')->nullable();
            $table->string('caption')->nullable();
            $table->string('description')->nullable();
            $table->string('geolocation')->nullable();
            $table->string('licence')->nullable();
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
        Schema::drop('asset_sources');
    }
}
