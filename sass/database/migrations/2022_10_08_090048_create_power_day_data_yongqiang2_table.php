<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePowerDayDataYongqiang2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('power_day_data_yongqiang2', function (Blueprint $table) {
            $table->id();
            $table->integer('power_map_id')->nullable()->comment('映射关系ID');
            $table->decimal('value', $precision = 20, $scale = 2)->nullable()->comment('累计值');
            $table->date('date')->nullable()->comment('累计日期');
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
        Schema::dropIfExists('power_day_data_yongqiang2');
    }
}
