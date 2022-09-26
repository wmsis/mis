<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistorianDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historian_data', function (Blueprint $table) {
            $table->id();
            $table->string('tag_name', 150)->nullable()->comment('tag中文名');
            $table->string('description', 150)->nullable()->comment('tag描述');
            $table->decimal('value')->nullable()->comment('值');
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
        Schema::dropIfExists('historian_data');
    }
}
