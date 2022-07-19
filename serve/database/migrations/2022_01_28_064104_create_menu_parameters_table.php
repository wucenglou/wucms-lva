<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('menu_table', 191)->comment('')->nullable();
            $table->bigInteger('menu_id')->unsigned()->nullable();
            $table->string('type', 191)->nullable();
            $table->string('key', 191)->nullable();
            $table->string('value', 191)->nullable();
            $table->dateTime('deleted_at')->nullable();
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
        Schema::dropIfExists('menu_parameters');
    }
}
