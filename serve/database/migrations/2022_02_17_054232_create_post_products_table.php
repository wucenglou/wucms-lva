<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_products', function (Blueprint $table) {
            $table->id();
            $table->string('cat_id', 50)->comment('分类ID');
            $table->integer('user_id')->comment('发布者ID');

            $table->integer('mode_id')->nullable()->comment('内容类型/模型id，是词条还是文章，1表示词条，2表示文章');
            $table->string('status', 50)->default('1')->comment('状态字段：1表示已发布，2表示待审核，3表示废弃。管理员默认已发布');

            $table->string('title', 191)->comment('标题');
            $table->string('keywords', 191)->comment('关键词')->nullable();
            $table->string('description')->comment('描述')->nullable();
            $table->text('content')->nullable()->comment('具体内容');
            
            $table->integer('sort')->default('0')->comment('排序字段，权重，数值越高越靠前');

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
        Schema::dropIfExists('post_products');
    }
}
