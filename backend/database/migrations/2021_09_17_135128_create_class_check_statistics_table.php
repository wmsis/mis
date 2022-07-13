<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassCheckStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_check_statistics', function (Blueprint $table) {
            $table->id();
            $table->integer('module_id')->nullable()->comment('锅炉ID');
            $table->dateTime('start_time',0)->nullable()->comment('报警开始时间');
            $table->dateTime('end_time',0)->nullable()->comment('报警结束时间');
            $table->string('tag_en_name', 250)->nullable()->comment('tag英文名');
            $table->string('tag_cn_name', 250)->nullable()->comment('tag中文名');
            $table->integer('value')->nullable()->comment('报警次数');
            $table->string('class_name')->nullable()->comment('班次名称');
            $table->string('duty_name')->nullable()->comment('值班人');
            $table->date('class_date')->nullable()->comment('班次日期');

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
        Schema::dropIfExists('class_check_statistics');
    }
}
