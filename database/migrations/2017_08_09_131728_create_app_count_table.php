<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppCountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_count', function (Blueprint $table) {
            $table->increments('id');
            $table->string('api_names', 200);
            $table->string('ip', 20);
            $table->string('app_id', 200);
            $table->integer('success', false, true);
            $table->integer('error', false, true);
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
        Schema::dropIfExists('api_count');
    }
}
