<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cat_menus', function (Blueprint $table) {
            $table->id();
            $table->string('parent_id',50)->comment('父栏目ID')->nullable();
            $table->string('mode_id', 191)->comment('组件id')->nullable();
            $table->string('meta_title', 191)->comment('栏目名称')->nullable();
            $table->bigInteger('menu_level')->unsigned()->nullable();
            $table->string('path', 191)->unique()->comment('路由path')->nullable();
            $table->string('name', 191)->unique()->comment('路由name')->nullable();
            $table->tinyInteger('hidden')->comment('是否在列表隐藏')->nullable();
            $table->integer('sort')->comment('排序标记')->default(0);

            $table->tinyInteger('meta_keep_alive')->comment('附加属性')->nullable();
            $table->tinyInteger('meta_default_menu')->comment('附加属性')->nullable();
            $table->string('meta_icon', 191)->nullable();
            $table->tinyInteger('mate_close_tab')->nullable();

            $table->string('seo_title', 191)->comment('seo_title')->nullable();
            $table->string('seo_keywords', 191)->comment('seo_keywords')->nullable();
            $table->string('seo_description', 191)->comment('seo_description')->nullable();

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
        Schema::dropIfExists('cat_menus');
    }
}
