<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAuthorityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_authorities', function (Blueprint $table) {
            $table->primary(['user_id', 'authority_id']);
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('authority_authority_id');
            $table->timestamps();
        });

        Schema::create('authorities', function (Blueprint $table) {
            $table->increments('authority_id');
            $table->string('parent_id', 191)->nullable()->comment('父角色ID');
            $table->string('authority_name',191)->nullable()->comment('角色名');
            $table->tinyInteger('authority_sys')->default(0);
            $table->string('authority_describe', 191)->nullable()->comment('角色描述');
            $table->string('default_router', 191)->default("dashboard")->comment('默认菜单');
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('authority_menus', function (Blueprint $table) {
            $table->primary(['authority_id', 'base_menu_id']);
            $table->string('authority_authority_id', 90);
            $table->unsignedBigInteger('base_menu_id');
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
        Schema::dropIfExists('user_authority');
        Schema::dropIfExists('authorities');
        Schema::dropIfExists('authority_menus');
    }
}
