<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnlineDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('online_data', function (Blueprint $table) {
            $table->id();
            $table->string('en_name', 100)->index()->nullable()->comment('参数英文名');
            $table->string('cn_name', 100)->index()->nullable()->comment('参数中文名');
            $table->date('date')->useCurrent()->nullable()->comment('采集日期');
            $table->float('value')->index()->nullable()->comment('累计值');
            $table->integer('total_seconds')->nullable()->comment('累计秒数');
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
        Schema::dropIfExists('online_data');
    }
}
