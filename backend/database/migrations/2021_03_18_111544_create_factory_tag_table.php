<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFactoryTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factory_tag', function (Blueprint $table) {
            $table->id();
            $table->string('cn_name', 100)->nullable()->comment('中文名称');
            $table->string('en_name', 100)->nullable()->comment('英文名称');
            $table->text('tag_name')->nullable()->comment('tag名称');
            $table->integer('min_value')->nullable()->comment('最小值');
            $table->integer('max_value')->nullable()->comment('最大值');
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
        Schema::dropIfExists('factory_tag');
    }
}
