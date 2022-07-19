<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modes', function (Blueprint $table) {
            $table->id();
            $table->string('parent_id',50)->comment('父模型ID')->default(0);
            $table->string('name', 191)->comment('模型名称')->nullable();
            $table->string('table', 50)->comment('模型数据表')->nullable()->default('post_articles');
            $table->string('component', 191)->comment('对应前端文件路径')->nullable();
            $table->string('desc', 191)->comment('模板描述')->nullable();
            $table->integer('sort')->comment('排序')->default(0);
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
        Schema::dropIfExists('modes');
    }
}
