<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFactoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historian_tag_yongqiang2', function (Blueprint $table) {
            $table->id();
            $table->string('tag_id', 50)->nullable()->comment('卡车号');
            $table->string('tag_name', 50)->nullable()->comment('tag中文名');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('electricity_day_data_yongqiang2', function (Blueprint $table) {
            $table->id();
            $table->integer('electricity_map_id')->nullable()->comment('电表映射关系ID');
            $table->decimal('value', $precision = 20, $scale = 2)->nullable()->comment('累计值');
            $table->date('date')->nullable()->comment('日期');

            $table->timestamps();
        });

        Schema::create('electricity_yongqiang2', function (Blueprint $table) {
            $table->id();
            $table->integer('electricity_map_id')->nullable()->comment('电表映射关系ID');
            $table->decimal('value', $precision = 20, $scale = 2)->nullable()->comment('远动获取的原始值');
            $table->decimal('actual_value', $precision = 20, $scale = 2)->nullable()->comment('实际值');

            $table->timestamps();
        });

        Schema::create('grab_garbage_day_data_yongqiang2', function (Blueprint $table) {
            $table->id();
            $table->integer('liao')->nullable()->comment('料口号');
            $table->decimal('value', $precision = 10, $scale = 2)->nullable()->comment('累计值');
            $table->date('date')->nullable()->comment('日期');

            $table->timestamps();
        });

        Schema::create('grab_garbage_yongqiang2', function (Blueprint $table) {
            $table->id();
            $table->integer('allsn')->nullable()->comment('流水号');
            $table->integer('sn')->nullable()->comment('称重记录序列');
            $table->integer('time')->nullable()->comment('时间戳');
            $table->tinyInteger('che')->nullable()->comment('行车号');
            $table->tinyInteger('dou')->nullable()->comment('料斗编号');
            $table->tinyInteger('liao')->nullable()->comment('料口编号');
            $table->tinyInteger('code')->nullable()->comment('暂未定义');
            $table->tinyInteger('lost')->nullable()->comment('抓斗采集丢包统计');
            $table->smallInteger('hev')->nullable()->comment('抓斗投料重量，单位KG	');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('power_day_data_yongqiang2', function (Blueprint $table) {
            $table->id();
            $table->integer('power_map_id')->nullable()->comment('用电映射关系ID');
            $table->decimal('value', $precision = 20, $scale = 2)->nullable()->comment('当日值');
            $table->date('date')->nullable()->comment('日期');

            $table->timestamps();
        });

        Schema::create('weighbridge_day_data_yongqiang2', function (Blueprint $table) {
            $table->id();
            $table->integer('weighbridge_cate_small_id')->nullable()->comment('垃圾分类小类ID');
            $table->decimal('value', $precision = 10, $scale = 2)->nullable()->comment('净重');
            $table->date('date')->nullable()->comment('日期');

            $table->timestamps();
        });

        Schema::create('weighbridge_format_yongqiang2', function (Blueprint $table) {
            $table->id();
            $table->dateTime('grossdatetime')->nullable()->comment('毛重时间');
            $table->dateTime('taredatetime')->nullable()->comment('皮重时间');
            $table->smallInteger('net')->nullable()->comment('净重');
            $table->integer('weighid')->nullable()->comment('称重磅单号');
            $table->integer('weighbridge_cate_small_id')->nullable()->comment('垃圾分类小类ID');

            $table->timestamps();
        });

        Schema::create('weighbridge_yongqiang2', function (Blueprint $table) {
            $table->id();
            $table->integer('weighid')->nullable()->comment('称重磅单号');
            $table->string('truckno', 50)->nullable()->comment('卡车号');
            $table->string('productcode', 50)->nullable()->comment('垃圾编号');
            $table->string('product', 50)->nullable()->comment('垃圾类型');
            $table->smallInteger('firstweight')->nullable()->comment('第一次称重');
            $table->smallInteger('secondweight')->nullable()->comment('第二次称重');
            $table->dateTime('firstdatetime')->nullable()->comment('第一次称重时间');
            $table->dateTime('seconddatetime')->nullable()->comment('第二次称重时间');
            $table->dateTime('grossdatetime')->nullable()->comment('毛重时间');
            $table->dateTime('taredatetime')->nullable()->comment('皮重时间');
            $table->string('sender', 50)->nullable()->comment('垃圾来源');
            $table->string('transporter', 50)->nullable()->comment('运输方');
            $table->string('receiver', 50)->nullable()->comment('接收方');
            $table->smallInteger('gross')->nullable()->comment('毛重');
            $table->smallInteger('tare')->nullable()->comment('皮重');
            $table->smallInteger('net')->nullable()->comment('净重');
            $table->tinyInteger('datastatus')->nullable()->comment('状态 0为删除，1为正常');

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
        Schema::dropIfExists('historian_tag_yongqiang2');
        Schema::dropIfExists('electricity_day_data_yongqiang2');
        Schema::dropIfExists('electricity_yongqiang2');
        Schema::dropIfExists('grab_garbage_day_data_yongqiang2');
        Schema::dropIfExists('grab_garbage_yongqiang2');
        Schema::dropIfExists('power_day_data_yongqiang2');
        Schema::dropIfExists('weighbridge_day_data_yongqiang2');
        Schema::dropIfExists('weighbridge_format_yongqiang2');
        Schema::dropIfExists('weighbridge_yongqiang2');
    }
}
