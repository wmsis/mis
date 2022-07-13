<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistorianModuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 新建 historian_module 表
         */
        Schema::create('historian_module', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 512)->index()->comment('模块名');
            $table->string('description', 512)->index()->comment('模块描述');
            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * historian_tag 增加外键约束
         */
        Schema::table('historian_tag', function (Blueprint $table) {
            $table->integer('historian_module_id')->unsigned()->nullable()->comment('tag所属模块外键约束');
            $table->foreign('historian_module_id')->references('id')->on('historian_module');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historian_tag', function (Blueprint $table) {
            $table->dropForeign('historian_module_id');
        });
        Schema::dropIfExists('historian_module');
    }
}
