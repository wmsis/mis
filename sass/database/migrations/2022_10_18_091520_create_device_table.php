<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //设备
        Schema::create('device', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->nullable()->comment('设备名称');
            $table->integer('parent_id')->nullable()->comment('父节点ID');
            $table->integer('ancestor_id')->nullable()->comment('祖先节点ID');
            $table->integer('orgnization_id')->nullable()->comment('所属组织ID');
            $table->integer('level')->nullable()->comment('所属层级');
            $table->integer('sort')->nullable()->comment('排序号');
            $table->date('quality_date')->nullable()->comment('质检日期');
            $table->date('factory_date')->nullable()->comment('出厂日期');
            $table->string('code', 50)->nullable()->comment('编码型号');
            $table->text('img')->nullable()->comment('图片');
            $table->integer('is_inspect')->nullable()->comment('是否巡检');
            $table->integer('is_group')->nullable()->comment('是否分组');

            $table->timestamps();
            $table->softDeletes();
        });

        //设备自定义属性
        Schema::create('device_property', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->nullable()->comment('属性名');
            $table->string('value', 50)->nullable()->comment('属性值');
            $table->integer('device_id')->nullable()->comment('设备ID');

            $table->timestamps();
            $table->softDeletes();
        });

        //任务
        Schema::create('task', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->nullable()->comment('任务名称');
            $table->enum('type', ['inspect', 'recondition', 'fix_defect'])->nullable()->comment('任务类型 inspect巡检, recondition检修, fix_defect消缺');
            $table->dateTime('begin')->nullable()->comment('开始时间');
            $table->dateTime('end')->nullable()->comment('结束时间');
            $table->integer('user_id')->nullable()->comment('用户ID');
            $table->integer('device_id')->nullable()->comment('设备ID');
            $table->string('content', 50)->nullable()->comment('任务内容');
            $table->dateTime('confirm_time')->nullable()->comment('确认时间');
            $table->enum('status', ['init', 'complete'])->nullable()->comment('任务状态 init发布状态  complete完成状态');
            $table->string('remark', 50)->nullable()->comment('备注');

            $table->timestamps();
            $table->softDeletes();
        });

        //报警
        Schema::create('alarm', function (Blueprint $table) {
            $table->id();
            $table->integer('alarm_rule_id')->nullable()->comment('设备报警规则ID');
            $table->string('content', 50)->nullable()->comment('报警内容');
            $table->dateTime('confirm_time')->nullable()->comment('确认时间');
            $table->enum('status', ['init', 'complete'])->nullable()->comment('任务状态 init初始状态  complete解决完成状态');

            $table->timestamps();
            $table->softDeletes();
        });

        //报警等级
        Schema::create('alarm_grade', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable()->comment('等级名称');
            $table->double('min_value', 10, 2)->nullable()->comment('最低值');
            $table->double('max_value', 10, 2)->nullable()->comment('最高值');
            $table->string('description', 50)->nullable()->comment('等级描述');

            $table->timestamps();
            $table->softDeletes();
        });

        //报警规则
        Schema::create('alarm_rule', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable()->comment('规则名称');
            $table->integer('device_id')->nullable()->comment('设备ID');
            $table->integer('dcs_standard_id')->nullable()->comment('标准名称ID');
            $table->integer('period')->nullable()->comment('周期时间（单位秒）');
            $table->integer('sustain')->nullable()->comment('持续周期数');
            $table->double('min_value', 10, 2)->nullable()->comment('最低值');
            $table->double('max_value', 10, 2)->nullable()->comment('最高值');
            $table->integer('alarm_grade_id')->nullable()->comment('报警等级ID');
            $table->enum('type', ['communication', 'scene'])->nullable()->comment('报警类型 communication通信, scene现场');
            $table->text('notify_user_ids', 50)->nullable()->comment('通知用户ID列表，英文逗号隔开');

            $table->timestamps();
            $table->softDeletes();
        });

        //通知
        Schema::create('notice', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable()->comment('用户ID');
            $table->enum('status', ['init', 'complete'])->nullable()->comment('任务状态 init初始状态  complete已读状态');
            $table->dateTime('confirm_time')->nullable()->comment('确认时间');
            $table->enum('type', ['alarm', 'announce'])->nullable()->comment('通知类型 alarm报警, announce通告');
            $table->integer('foreign_id')->nullable()->comment('外键ID值，type=alarm时为alarm的ID，type=announce时为announcement的ID');

            $table->timestamps();
            $table->softDeletes();
        });

        //公告
        Schema::create('announcement', function (Blueprint $table) {
            $table->id();
            $table->string('title', 50)->nullable()->comment('通告内容');
            $table->text('content')->nullable()->comment('通告内容');
            $table->text('notify_user_ids', 50)->nullable()->comment('通知用户ID列表，英文逗号隔开');

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
        Schema::dropIfExists('device');
        Schema::dropIfExists('device_property');
        Schema::dropIfExists('task');
        Schema::dropIfExists('alarm');
        Schema::dropIfExists('alarm_grade');
        Schema::dropIfExists('alarm_rule');
        Schema::dropIfExists('notice');
        Schema::dropIfExists('announcement');
    }
}
