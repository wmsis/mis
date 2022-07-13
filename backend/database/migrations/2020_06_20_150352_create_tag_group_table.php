<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_group', function (Blueprint $table) {
            $table->id();
            $table->string('name', 256)->unique()->index()->comment('组名');
            $table->string('description', 2048)->nullable()->comment('描述');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('historian_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('tag_group_id')->nullable()->comment('Tag Group ID');
            $table->foreign('tag_group_id')->references('id')->on('tag_group');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tag_group');
        Schema::table('historian_tag', function (Blueprint $table) {
            $table->dropForeign('tag_group_id');
        });
    }
}
