<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //巡检规则
        Schema::create('inspect_rule', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable()->comment('规则名');
            $table->integer('device_property_id')->nullable()->comment('设备属性ID');
            $table->text('content')->nullable()->comment('规则内容');
            $table->text('standard')->nullable()->comment('巡检标准');
            $table->integer('orgnization_id')->nullable()->comment('组织ID');
            $table->timestamps();
            $table->softDeletes();
        });

        //设备自定义设备属性模板
        Schema::create('device_property_template', function (Blueprint $table) {
            $table->id();
            $table->integer('device_template_id')->nullable()->comment('设备模板ID');
            $table->string('name', 50)->nullable()->comment('属性模板名');
            $table->enum('type', ['text', 'integer', 'image', 'date', 'radio', 'checkbox', 'select', 'switch'])->nullable()->comment('模板类型 text文本, integer数字, image图片, date日期, radio单选, checkbox多选, select下拉列表, switch开关');
            $table->text('value')->nullable()->comment('文本框为单个值，列表为多个值，英文逗号隔开');
            $table->string('default_value', 50)->nullable()->comment('默认值');
            $table->integer('orgnization_id')->nullable()->comment('组织ID');

            $table->timestamps();
            $table->softDeletes();
        });

        //设备巡检规则关联表
        Schema::create('task_inspect_rule', function (Blueprint $table) {
            $table->id();
            $table->integer('task_id')->nullable()->comment('属性值');
            $table->integer('inspect_rule_id')->nullable()->comment('设备ID');

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
        Schema::dropIfExists('inspect_rule');
        Schema::dropIfExists('device_property_template');
        Schema::dropIfExists('task_inspect_rule');
    }
}
