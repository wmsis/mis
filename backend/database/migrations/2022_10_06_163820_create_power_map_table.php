<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePowerMapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('power_map', function (Blueprint $table) {
            $table->id();
            $table->string('electricity_map_ids', 150)->nullable()->comment('electricity_map主键列表');
            $table->string('dcs_standard_id')->nullable()->comment('标准名称表主键');
            $table->text('func')->nullable()->comment('值');
            $table->integer('orgnization_id')->nullable()->comment('所属组织');

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
        Schema::dropIfExists('power_map');
    }
}
