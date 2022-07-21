<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeighbridgeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weighbridge_yongqiang2', function (Blueprint $table) {
            $table->id();
            $table->string('truckno', 30)->nullable()->comment('卡车车牌号码');
            $table->string('productcode', 30)->nullable()->comment('货物名称');
            $table->string('product', 30)->nullable()->comment('货物名称');
            $table->unsignedSmallInteger('firstweight')->nullable()->comment('第一次称重，单位KG');
            $table->unsignedSmallInteger('secondweight')->nullable()->comment('第二次称重，单位KG');
            $table->datetime('firstdatetime')->nullable()->comment('第一次称重时间');
            $table->datetime('seconddatetime')->nullable()->comment('第二次称重时间');
            $table->datetime('grossdatetime')->nullable()->comment('毛重时间');
            $table->datetime('taredatetime')->nullable()->comment('皮重时间');
            $table->string('sender', 50)->nullable()->comment('发送方');
            $table->string('transporter', 50)->nullable()->comment('运输方');
            $table->string('receiver', 50)->nullable()->comment('接收方');
            $table->unsignedSmallInteger('gross')->nullable()->comment('毛重，单位KG');
            $table->unsignedSmallInteger('tare')->nullable()->comment('皮重，单位KG');
            $table->unsignedSmallInteger('net')->nullable()->comment('净重，单位KG');
            $table->tinyInteger('datastatus')->nullable()->comment('状态：0为删除，1为正常');
            $table->unsignedBigInteger('weighid')->nullable()->comment('AVS主键ID');

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
        Schema::dropIfExists('weighbridge_yongqiang2');
    }
}
