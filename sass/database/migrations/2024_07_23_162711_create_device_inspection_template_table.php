<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceInspectionTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_inspection_template', function (Blueprint $table) {
            $table->id();
            $table->integer('device_template_id')->nullable()->comment('设备模板ID');
            $table->string('group_id', 50)->nullable()->comment('分组ID');
            $table->string('name', 50)->nullable()->comment('巡检属性名');
            $table->string('type', 30)->nullable()->comment('属性类型 text文本, integer数字, image图片, date日期, radio单选, checkbox多选, select下拉列表, switch开关');
            $table->text('value')->nullable()->comment('巡检属性值');
            $table->string('default_value', 50)->nullable()->comment('巡检属性默认值');
            $table->integer('sort')->nullable()->comment('排序号');
            $table->integer('orgnization_id')->nullable()->comment('组织ID');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inspect_point', function (Blueprint $table) {
            $table->id();
            $table->integer('device_id')->nullable()->comment('设备ID');
            $table->string('address', 150)->nullable()->comment('地址');
            $table->text('remark')->nullable()->comment('备注');
            $table->integer('sort')->nullable()->comment('排序号');
            $table->integer('orgnization_id')->nullable()->comment('组织ID');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inspect_line', function (Blueprint $table) {
            $table->id();
            $table->text('remark')->nullable()->comment('备注');
            $table->integer('sort')->nullable()->comment('排序号');
            $table->integer('orgnization_id')->nullable()->comment('组织ID');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inspect_line_point', function (Blueprint $table) {
            $table->id();
            $table->integer('inspect_line_id')->nullable()->comment('巡检线路ID');
            $table->integer('inspect_point_id')->nullable()->comment('巡检点位ID');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inspect_plan', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable()->comment('巡检计划名');
            $table->integer('inspect_line_id')->nullable()->comment('巡检线路ID');
            $table->enum('period', ['day', 'week', 'month'])->nullable()->comment('周期类型 day按日, week按周, month按月');
            $table->string('start', 30)->nullable()->comment('开始日期');
            $table->string('end', 30)->nullable()->comment('结束日期');
            $table->integer('user_id')->nullable()->comment('执行用户ID');
            $table->integer('status')->nullable()->comment('状态 0正常 1禁用');
            $table->text('remark')->nullable()->comment('备注');
            $table->integer('sort')->nullable()->comment('排序号');
            $table->integer('orgnization_id')->nullable()->comment('组织ID');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inspect_todo', function (Blueprint $table) {
            $table->id();
            $table->integer('inspect_plan_id')->nullable()->comment('巡检计划ID');
            $table->integer('inspect_line_id')->nullable()->comment('巡检线路ID');
            $table->string('date', 30)->nullable()->comment('日期');
            $table->integer('user_id')->nullable()->comment('执行用户ID');
            $table->integer('status')->nullable()->comment('状态 0待巡检 1已巡检');
            $table->string('img', 150)->nullable()->comment('巡检图片地址');
            $table->text('content')->nullable()->comment('巡检结果');
            $table->text('remark')->nullable()->comment('备注');
            $table->integer('orgnization_id')->nullable()->comment('组织ID');

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
        Schema::dropIfExists('device_inspection_template');
        Schema::dropIfExists('inspect_point');
        Schema::dropIfExists('inspect_line');
        Schema::dropIfExists('inspect_line_point');
        Schema::dropIfExists('inspect_plan');
        Schema::dropIfExists('inspect_todo');
    }
}
