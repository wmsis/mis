<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagRememberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_remember', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('path', 512)->comment('path路径');
            $table->unsignedBigInteger('user_id')->comment('user id');
            $table->text('tag_ids')->nullable()->comment('historian tag id(主键)');
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
        Schema::dropIfExists('tag_remember');
    }
}
