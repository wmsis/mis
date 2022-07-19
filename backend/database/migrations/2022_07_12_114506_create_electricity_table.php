<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElectricityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('electricity_yongqiang2', function (Blueprint $table) {
            $table->id();
            $table->integer('address')->nullable()->comment('地址');
            $table->integer('value')->nullable()->comment('远动获取的原始值');
            $table->decimal('actual_value', 12, 2)->nullable()->comment('实际值');
            $table->integer('quality')->nullable()->comment('品质描述');
            $table->decimal('factor', 12, 2)->nullable()->comment('系素');
            $table->string('cn_name', 50)->nullable()->comment('中文名称');

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
        Schema::dropIfExists('electricity_yongqiang2');
    }
}
