<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('name', 512)->index()->unique()->comment('设备名称');
            $table->string('model', 512)->index()->nullable()->comment('设备型号');
            $table->string('manufacturer', 512)->nullable()->comment('生产厂家');
            $table->string('serial_number', 512)->nullable()->comment('出厂编号');
            $table->date('production_date')->useCurrent()->nullable()->comment('投产日期');
            $table->enum('status', ['active', 'storage','waste'])->nullable()->comment('设备状态');
            $table->string('charge_person_name', 100)->nullable()->comment('责任人姓名');
            $table->string('charge_person_phone', 100)->nullable()->comment('责任人电话');
            $table->string('work_location', 100)->nullable()->comment('服役位置');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipment');
    }
}
