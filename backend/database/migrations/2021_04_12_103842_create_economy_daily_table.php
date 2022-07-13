<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEconomyDailyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         Schema::create('economy_daily', function (Blueprint $table) {
             $table->id();
             $table->string('en_name', 100)->index()->nullable()->comment('参数英文名');
             $table->string('cn_name', 100)->index()->nullable()->comment('参数中文名');
             $table->string('value', 100)->index()->nullable()->comment('参数值');
             $table->string('time', 100)->nullable()->comment('参数日期');
             $table->string('period', 30)->nullable()->comment('区间');
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
         Schema::dropIfExists('economy_daily');
     }
}
