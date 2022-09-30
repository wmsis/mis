<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrabGarbageDayDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grab_garbage_day_data_yongqiang2', function (Blueprint $table) {
            $table->id();
            $table->integer('liao')->nullable()->comment('料口号');
            $table->decimal('value', $precision = 8, $scale = 2)->nullable()->comment('累计值');
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
        Schema::dropIfExists('grab_garbage_day_data_yongqiang2');
    }
}
