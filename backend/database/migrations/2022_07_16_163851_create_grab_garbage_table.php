<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrabGarbageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grab_garbage_yongqiang2', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('allsn')->nullable()->comment('流水号');
            $table->unsignedInteger('sn')->nullable()->comment('称重记录序列');
            $table->unsignedInteger('time')->nullable()->comment('时间戳');
            $table->tinyInteger('che')->nullable()->comment('行车号');
            $table->tinyInteger('dou')->nullable()->comment('料斗编号');
            $table->tinyInteger('liao')->nullable()->comment('料口编号');
            $table->tinyInteger('code')->nullable()->comment('暂未定义');
            $table->tinyInteger('lost')->nullable()->comment('抓斗采集丢包统计');
            $table->unsignedSmallInteger('hev')->nullable()->comment('抓斗投料重量，单位KG');
            $table->index('time');

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
        Schema::dropIfExists('grab_garbage_yongqiang2');
    }
}
