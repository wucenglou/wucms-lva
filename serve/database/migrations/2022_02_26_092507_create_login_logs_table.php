<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->default('')->comment("用户id");
            $table->string('username', 50)->default('')->comment("登录名称");

            $table->string('address')->default('')->comment("登录地点");

            $table->string('ip', 63)->default('')->comment("登录ip");
            $table->string('useragent')->default('')->comment("useragent原始信息");

            $table->string('browser',50)->default('')->comment("浏览器");
            $table->string('browser_ver', 50)->default('')->comment("浏览器版本");
            $table->string('device',50)->default('')->comment("设备");
            $table->string('device_type', 50)->default('')->comment("设备版本");
            $table->string('platform',50)->default('')->comment("操作系统平台");
            $table->string('platform_ver',50)->default('')->comment("操作系统平台版本");
            $table->string('status',5)->default('')->comment("登录状态");
            $table->string('msg')->default('')->comment("操作信息");
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
        Schema::dropIfExists('login_log');
    }
};
