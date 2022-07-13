<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoilerTurbineStatementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boiler_turbine_statement', function (Blueprint $table) {
            $table->id();
            $table->string('en_name', 100)->index()->nullable()->comment('参数英文名');
            $table->string('cn_name', 100)->index()->nullable()->comment('参数中文名');
            $table->string('value', 100)->index()->nullable()->comment('参数值');
            $table->date('date')->nullable()->comment('日期');
            $table->string('measure', 50)->nullable()->comment('单位');
            $table->integer('module_id')->nullable()->comment('模块id');
            $table->string('time', 50)->nullable()->comment('时间');
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
        Schema::dropIfExists('boiler_turbine_statement');
    }
}
