<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParamRememberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('param_remember', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('path', 512)->comment('path路径');
            $table->unsignedBigInteger('user_id')->comment('user id');
            $table->text('params')->nullable()->comment('输入的参数列表');
            $table->unique(['path', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('param_remember');
    }
}
