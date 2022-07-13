<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTurbineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('turbine', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable()->comment('汽轮机名称');
            $table->string('hash', 50)->nullable()->comment('hash');
            $table->string('electricity_tag', 250)->nullable()->comment('功率tag名称');
            $table->string('electricity_ability', 50)->nullable()->comment('设计日发电功率');
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
        Schema::dropIfExists('turbine');
    }
}
