<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfflineDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offline_data', function (Blueprint $table) {
            $table->id();
            $table->string('en_name', 100)->index()->nullable()->comment('参数英文名');
            $table->string('cn_name', 100)->index()->nullable()->comment('参数中文名');
            $table->string('value', 100)->index()->nullable()->comment('参数值');
            $table->string('time', 100)->nullable()->comment('参数日期');
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
        Schema::dropIfExists('offline_data');
    }
}
