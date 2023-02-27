<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassSchduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //排班计划详情
        Schema::create('class_schdule', function (Blueprint $table) {
            $table->id();
            $table->integer('orgnization_id')->nullable()->comment('组织ID');
            $table->integer('user_id')->nullable()->comment('用户ID');
            $table->date('date')->nullable()->comment('排班日期');
            $table->string('class_define_name', 30)->nullable()->comment('排班班次名称');
            $table->string('start', 30)->nullable()->comment('排班起始时间');//可能出现延迟交班情况，手动更改时间
            $table->string('end', 30)->nullable()->comment('排班终止时间');
            $table->string('class_group_name', 50)->nullable()->comment('排班班组名称'); //人会会变动，此处不记录班组ID
            $table->timestamps();
            $table->softDeletes();
        });

        //班次
        Schema::create('class_define', function (Blueprint $table) {
            $table->id();
            $table->integer('orgnization_id')->nullable()->comment('组织ID');
            $table->string('name', 30)->nullable()->comment('班次名称');
            $table->string('start', 30)->nullable()->comment('起始时间');
            $table->string('end', 30)->nullable()->comment('终止时间');
            $table->timestamps();
            $table->softDeletes();
        });

        //班组
        Schema::create('class_group', function (Blueprint $table) {
            $table->id();
            $table->integer('orgnization_id')->nullable()->comment('组织ID');
            $table->string('name', 30)->nullable()->comment('班组名称');
            $table->integer('charge_user_id')->nullable()->comment('值长用户ID');
            $table->text('user_ids')->nullable()->comment('班组成员用户ID');
            $table->timestamps();
            $table->softDeletes();
        });

        //排班周期
        Schema::create('class_loop', function (Blueprint $table) {
            $table->id();
            $table->integer('orgnization_id')->nullable()->comment('组织ID');
            $table->string('name', 30)->nullable()->comment('排班周期名称');
            $table->integer('sort')->nullable()->comment('排序号');
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
        Schema::dropIfExists('class_schdule');
        Schema::dropIfExists('class_define');
        Schema::dropIfExists('class_group');
        Schema::dropIfExists('class_loop');
    }
}
