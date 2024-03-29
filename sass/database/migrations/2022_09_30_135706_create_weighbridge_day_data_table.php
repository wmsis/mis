<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeighbridgeDayDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weighbridge_day_data_yongqiang2', function (Blueprint $table) {
            $table->id();
            $table->integer('weighbridge_cate_small_id')->nullable()->comment('垃圾小分类ID');
            $table->decimal('value', $precision = 10, $scale = 2)->nullable()->comment('累计值');
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
        Schema::dropIfExists('weighbridge_day_data_yongqiang2');
    }
}
