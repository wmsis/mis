<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiyClassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diy_class', function (Blueprint $table) {
            $table->id();
            $table->string('date', 50)->index()->nullable()->comment('日期');
            $table->integer('module_id')->nullable()->comment('锅炉模块ID');
            $table->string('morning_value', 50)->nullable()->comment('早班值日人');
            $table->dateTime('morning_begin', 0)->nullable()->comment('早班开始时间');
            $table->dateTime('morning_end', 0)->nullable()->comment('早班结束时间');
            $table->string('day_value', 50)->nullable()->comment('白班值日人');
            $table->dateTime('day_begin', 0)->nullable()->comment('白班开始时间');
            $table->dateTime('day_end', 0)->nullable()->comment('白班结束时间');
            $table->string('evening_value', 50)->nullable()->comment('中班值日人');
            $table->dateTime('evening_begin', 0)->nullable()->comment('中班开始时间');
            $table->dateTime('evening_end', 0)->nullable()->comment('中班结束时间');
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
        Schema::dropIfExists('diy_class');
    }
}
