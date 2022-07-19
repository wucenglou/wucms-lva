<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();
            $table->string('username');
            $table->string('nick_name')->default("");
            $table->string('gender')->default("");
            $table->string('intro')->nullable();
            $table->string('avatar_url')->default("https://qmplusimg.henrongyi.top/gva_header.jpg");
            $table->string('side_mode')->default("dark");
            $table->string('base_color')->default("#fff");
            $table->string('active_color')->default("#1890ff");
            $table->string('email')->nullable();
            $table->string('phone_num',50)->nullable()->comment('账户绑定的手机号');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('last_ip')->nullable();
            $table->string('authority_id')->default("0");
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
