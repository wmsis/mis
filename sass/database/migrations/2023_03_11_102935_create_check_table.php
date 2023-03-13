<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //考核分（发电量）详情
        Schema::create('check_point_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('orgnization_id')->nullable()->comment('组织ID');
            $table->integer('user_id')->nullable()->comment('用户ID');
            $table->date('date')->nullable()->comment('考核日期');
            $table->decimal('value', $precision = 20, $scale = 2)->nullable()->comment('累计值');
            $table->string('reason', 100)->nullable()->comment('具体原因');
            $table->enum('type', ['class', 'alarm', 'daily'])->nullable()->comment('考核类型 class上班 alarm报警 daily日常考核')->default('class');
            $table->integer('check_tag_detail_id')->nullable()->comment('考核班次报警详情ID');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('check_tag', function (Blueprint $table) {
            $table->id();
            $table->integer('orgnization_id')->nullable()->comment('组织ID');
            $table->integer('dcs_map_id')->nullable()->comment('标准映射关系ID');
            $table->string('remark', 100)->nullable()->comment('扣款标准明细');
            $table->integer('point_every_alarm')->nullable()->comment('每次报警扣多少发电量');
            $table->text('user_ids')->nullable()->comment('涉及考核人员ID列表');
            $table->timestamps();
            $table->softDeletes();
        });

        //每个人某天的考核指标原始数字
        Schema::create('check_tag_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable()->comment('用户ID');
            $table->date('date')->nullable()->comment('考核日期');//每人每天只记录一次
            $table->integer('check_tag_id')->nullable()->comment('考核指标ID');
            $table->integer('first_alarm_num')->nullable()->comment('上班开始时间报警次数');
            $table->integer('second_alarm_num')->nullable()->comment('上班结束时间报警次数');
            $table->integer('class_alarm_num')->nullable()->comment('上班期间内报警次数');
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
        Schema::dropIfExists('check_point_detail');
        Schema::dropIfExists('check_tag');
        Schema::dropIfExists('check_tag_detail');
    }
}
