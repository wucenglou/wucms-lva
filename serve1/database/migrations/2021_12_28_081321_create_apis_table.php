<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apis', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->default(0);  //api类型 0 未知／1 通过／ -1 删除
            $table->integer('sort')->default(0);
            $table->string('path')->unique();
            $table->string('description')->default('');
            $table->string('api_group')->default('');
            $table->string('methods')->default(''); // 
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
        Schema::dropIfExists('apis');
    }
}
