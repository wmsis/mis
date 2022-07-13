<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassStatementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_statement', function (Blueprint $table) {
            $table->id();
            $table->string('tag_en_name', 150)->nullable()->comment('tag英文名');
            $table->string('tag_cn_name', 150)->nullable()->comment('tag中文名');
            $table->integer('value')->nullable()->comment('值');
            $table->string('class_name')->nullable()->comment('班次名称');
            $table->string('duty_name')->nullable()->comment('值班人');
            $table->date('date')->nullable()->comment('班次日期');

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
        Schema::dropIfExists('class_statement');
    }
}
