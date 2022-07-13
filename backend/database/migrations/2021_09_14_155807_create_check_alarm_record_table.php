<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckAlarmRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_alarm_record', function (Blueprint $table) {
            $table->id();
            $table->integer('boiler_check_id')->nullable()->comment('锅炉考核指标ID');
            $table->integer('module_id')->nullable()->comment('锅炉ID');
            $table->dateTime('start_time', 0)->nullable()->comment('报警开始时间');
            $table->dateTime('end_time', 0)->nullable()->comment('报警结束时间');
            $table->string('tag_en_name', 250)->nullable()->comment('tag英文名');
            $table->string('tag_cn_name', 250)->nullable()->comment('tag中文名');
            $table->string('upper_limit', 50)->nullable()->comment('报警上限');
            $table->string('lower_limit', 50)->nullable()->comment('报警下限');
            $table->string('time_limit', 50)->nullable()->comment('报警时限');
            $table->integer('is_notify')->nullable()->comment('是否已通知');
            $table->integer('need_notify')->nullable()->comment('是否需要通知');
            $table->text('notify_member_ids')->nullable()->comment('通知用户ID');

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
        Schema::dropIfExists('check_alarm_record');
    }
}
