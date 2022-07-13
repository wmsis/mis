<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentSparePartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_spare_part', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('serial_number', 512)->comment('序号');
            $table->string('name', 512)->index()->comment('备品名称');
            $table->string('model', 512)->nullable()->index()->comment('规格型号');
            $table->integer('equipped_quantity')->nullable()->comment('在装数量');
            $table->string('manufacturer', 512)->nullable()->comment('生产厂家');
            $table->text('remark')->nullable()->comment('备注');
            $table->foreignId('equipment_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipment_spare_part');
    }
}
