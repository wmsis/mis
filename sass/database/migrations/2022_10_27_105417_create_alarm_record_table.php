<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlarmRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alarm_record', function (Blueprint $table) {
            $table->id();
            $table->integer('alarm_rule_id')->nullable()->comment('报警规则ID');
            $table->dateTime('start_time')->nullable()->comment('开始时间');
            $table->dateTime('end_time')->nullable()->comment('结束时间');
            $table->double('max_value', 10, 2)->nullable()->comment('设置的最高值');
            $table->double('min_value', 10, 2)->nullable()->comment('设置的最低值');
            $table->integer('orgnization_id')->nullable()->comment('组织ID');
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
        Schema::dropIfExists('alarm_record');
    }
}
