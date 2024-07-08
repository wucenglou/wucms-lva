<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaseMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('base_menus', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('menu_level')->unsigned()->nullable();
            $table->string('parent_id',50)->comment('父菜单ID')->nullable();
            $table->string('path', 191)->unique()->comment('路由path')->nullable();
            $table->string('name', 191)->unique()->comment('路由name')->nullable();
            $table->tinyInteger('hidden')->comment('是否在列表隐藏')->nullable();
            $table->string('component', 191)->comment('对应前端文件路径')->nullable();
            $table->integer('sort')->comment('排序标记')->default(0);
            $table->tinyInteger('keep_alive')->comment('附加属性')->nullable();
            $table->tinyInteger('default_menu')->comment('附加属性')->nullable();
            $table->string('title', 191)->nullable();
            $table->string('icon', 191)->nullable();
            $table->tinyInteger('close_tab')->default(0);
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
        Schema::dropIfExists('base_menus');
    }
}
