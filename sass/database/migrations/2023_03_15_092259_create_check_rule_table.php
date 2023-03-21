<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //考核准则
        Schema::create('check_rule', function (Blueprint $table) {
            $table->id();
            $table->integer('orgnization_id')->nullable()->comment('组织ID');
            $table->string('name', 30)->nullable()->comment('指标名称');
            $table->integer('value')->nullable()->comment('指标考勤发电量');
            $table->string('remark', 100)->nullable()->comment('指标说明备注');
            $table->text('dcs_standard_ids')->nullable()->comment('考核指标列表');
            $table->enum('type', ['technology', 'daily'])->nullable()->comment('指标类型  技术类和日常类');
            $table->integer('check_rule_group_id')->nullable()->comment('考核指标分组ID');
            $table->integer('isopen')->nullable()->comment('是否启用');
            $table->timestamps();
            $table->softDeletes();
        });

        //考核指标分组
        Schema::create('check_rule_group', function (Blueprint $table) {
            $table->id();
            $table->integer('orgnization_id')->nullable()->comment('组织ID');
            $table->string('name', 30)->nullable()->comment('组名');
            $table->string('description', 100)->nullable()->comment('说明备注');
            $table->timestamps();
            $table->softDeletes();
        });

        //考核准则分配
        Schema::create('check_rule_allocation', function (Blueprint $table) {
            $table->id();
            $table->integer('check_rule_id')->nullable()->comment('考核指标ID');
            $table->integer('job_station_id')->nullable()->comment('岗位ID');
            $table->decimal('percent', $precision = 8, $scale = 2)->nullable()->comment('百分比');
            $table->integer('isopen')->nullable()->comment('是否启用');
            $table->timestamps();
            $table->softDeletes();
        });

        //岗位
        Schema::create('job_station', function (Blueprint $table) {
            $table->id();
            $table->integer('orgnization_id')->nullable()->comment('组织ID');
            $table->string('name', 30)->nullable()->comment('岗位名称');
            $table->string('description', 50)->nullable()->comment('岗位描述');
            $table->timestamps();
            $table->softDeletes();
        });

        //收入分配规则
        Schema::create('class_group_allocation', function (Blueprint $table) {
            $table->id();
            $table->integer('orgnization_id')->nullable()->comment('组织ID');
            $table->string('class_group_name', 30)->nullable()->comment('班组名称');
            $table->integer('isopen')->nullable()->comment('是否启用');
            $table->timestamps();
            $table->softDeletes();
        });

        //班组收入分配详情
        Schema::create('class_group_allocation_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('class_group_allocation_id')->nullable()->comment('收入分配规则ID');
            $table->string('job_station_id', 30)->nullable()->comment('岗位ID');
            $table->decimal('percent', $precision = 8, $scale = 2)->nullable()->comment('百分比');
            $table->timestamps();
            $table->softDeletes();
        });

        //考核动作打分  技术类和日常类考核详情
        Schema::create('check_action_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('orgnization_id')->nullable()->comment('组织ID');
            $table->integer('check_rule_id')->nullable()->comment('考核指标ID');
            $table->integer('value')->nullable()->comment('考核考勤发电量');
            $table->enum('type', ['personal', 'group'])->nullable()->comment('考核类型  班组group或个人personal');
            $table->date('date')->nullable()->comment('考核日期');
            $table->timestamps();
            $table->softDeletes();
        });

        //考核班组分配详情
        Schema::create('check_action_detail_group_allocation', function (Blueprint $table) {
            $table->id();
            $table->string('class_group_name', 30)->nullable()->comment('班组名称');
            $table->integer('check_action_detail_id')->nullable()->comment('考核动作详情ID');
            $table->string('job_station_id', 30)->nullable()->comment('岗位ID');
            $table->integer('user_id')->nullable()->comment('用户ID');
            $table->decimal('percent', $precision = 8, $scale = 2)->nullable()->comment('百分比');
            $table->integer('value')->nullable()->comment('考核考勤发电量');
            $table->timestamps();
            $table->softDeletes();
        });

        //考核个人分配详情
        Schema::create('check_action_detail_personal_allocation', function (Blueprint $table) {
            $table->id();
            $table->integer('check_action_detail_id')->nullable()->comment('考核动作详情ID');
            $table->integer('user_id')->nullable()->comment('用户ID');
            $table->decimal('percent', $precision = 8, $scale = 2)->nullable()->comment('百分比');
            $table->integer('value')->nullable()->comment('考核考勤发电量');
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
        Schema::dropIfExists('check_rule');
        Schema::dropIfExists('check_rule_group');
        Schema::dropIfExists('check_rule_allocation');
        Schema::dropIfExists('job_station');
        Schema::dropIfExists('class_group_allocation');
        Schema::dropIfExists('class_group_allocation_detail');
        Schema::dropIfExists('check_action_detail');
        Schema::dropIfExists('check_action_detail_group_allocation');
        Schema::dropIfExists('check_action_detail_personal_allocation');
    }
}
