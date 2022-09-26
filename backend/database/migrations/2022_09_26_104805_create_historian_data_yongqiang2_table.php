<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistorianDataYongqiang2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historian_data_yongqiang2', function (Blueprint $table) {
            $table->id();
            $table->string('tag_name', 150)->nullable()->comment('tag中文名');
            $table->string('description', 150)->nullable()->comment('tag描述');
            $table->decimal('value', $precision = 8, $scale = 2)->nullable()->comment('值');
            $table->dateTime('datetime')->nullable()->comment('取值时间');

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
        Schema::dropIfExists('historian_data_yongqiang2');
    }
}
