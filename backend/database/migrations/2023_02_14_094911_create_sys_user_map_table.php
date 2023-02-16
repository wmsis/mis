<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSysUserMapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_user_map', function (Blueprint $table) {
            $table->id();
            $table->string('basic_sys_name', 50)->nullable()->comment('基本系统名称');
            $table->string('basic_conn_name', 50)->nullable()->comment('基本系统DB连接名称');
            $table->string('basic_domian', 50)->nullable()->comment('基本系统域名');
            $table->integer('basic_user_id')->nullable()->comment('基本系统用户ID');
            $table->string('basic_login_path', 50)->nullable()->comment('基本系统登录路径');
            $table->string('basic_token', 100)->nullable()->comment('基本系统token');
            $table->string('target_sys_name', 50)->nullable()->comment('目标系统名称');
            $table->string('target_conn_name', 50)->nullable()->comment('目标系统DB连接名称');
            $table->string('target_domian', 50)->nullable()->comment('目标系统域名');
            $table->integer('target_user_id')->nullable()->comment('目标系统用户ID');
            $table->string('target_login_path', 50)->nullable()->comment('目标系统登录路径');
            $table->string('target_token', 100)->nullable()->comment('目标系统token');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_user_map');
    }
}
