<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cat_id')->default('')->comment("属于哪个分类,分类id");

            $table->string('mode_id')->default('')->comment('哪个表id');
            $table->string('post_id')->default('')->comment('哪个表的id');

            $table->integer('sort')->default('0')->comment('排序字段，权重，数值越高越靠前');
            $table->string('title')->default('');
            $table->string('desc')->nullable();
            $table->string('url', 255);
            $table->integer('status')->default(1);
            $table->integer('from')->nullable();

            $table->softDeletes();
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
        Schema::dropIfExists('post_files');
    }
}
