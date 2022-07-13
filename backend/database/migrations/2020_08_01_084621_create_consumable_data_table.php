<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsumableDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consumable_data', function (Blueprint $table) {
            $table->id();
            $table->string('en_name', 100)->index()->nullable()->comment('参数英文名');
            $table->string('cn_name', 100)->index()->nullable()->comment('参数中文名');
            $table->string('value', 100)->index()->nullable()->comment('参数值');
            $table->date('date')->nullable()->comment('日期');
            $table->string('start', 100)->nullable()->comment('上班开始时间');
            $table->string('end', 100)->nullable()->comment('上班结束时间');
            $table->string('measure', 50)->nullable()->comment('单位');
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
        Schema::dropIfExists('consumable_data');
    }
}
