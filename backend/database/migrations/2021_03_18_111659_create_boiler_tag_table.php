<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoilerTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boiler_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boiler_id')->nullable()->comment('锅炉ID');
            $table->string('category', 100)->nullable()->comment('所属分类');
            $table->string('cn_name', 100)->nullable()->comment('中文名称');
            $table->string('tag_name', 100)->nullable()->comment('tag名称');
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
        Schema::dropIfExists('boiler_tag');
    }
}
