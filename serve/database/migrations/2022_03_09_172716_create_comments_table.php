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
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('parent_id', 50)->default('0')->comment('父评论ID');
            $table->integer('user_id');
            $table->string('mode_id')->default('')->comment('哪个表id');
            $table->string('post_id')->default('')->comment('哪个表的id');
            $table->text('content');
            $table->string('status', 5)->default(1)->comment('评论状态');
            $table->string('user_agent', 200)->default('')->comment("useragent原始信息");
            $table->string('userip', 63)->nullable()->comment('使用者ip');
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
        Schema::dropIfExists('comments');
    }
};
