<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineRunStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_run_status', function (Blueprint $table) {
            $table->id();
            $table->string('device_name', 50)->nullable()->comment('设备名称');
            $table->string('tag_names', 250)->nullable()->comment('tagnames');
            $table->enum('status', ['run', 'stop'])->index()->nullable()->comment('设备运行状态');
            $table->bigInteger('this_time_runtime')->nullable()->comment('当次运行时间(秒)');
            $table->bigInteger('total_runtime')->nullable()->comment('总运行时间(秒)');
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
        Schema::dropIfExists('machine_run_status');
    }
}
