<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoilerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boiler', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable()->comment('锅炉名称');
            $table->string('hash', 50)->nullable()->comment('hash');
            $table->text('rubbish_ability')->nullable()->comment('设计垃圾处理能力');
            $table->text('rubbish_entry_tag')->nullable()->comment('垃圾入炉量tag');
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
        Schema::dropIfExists('boiler');
    }
}
