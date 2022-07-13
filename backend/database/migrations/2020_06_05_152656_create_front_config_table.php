<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFrontConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('front_config', function (Blueprint $table) {
            $table->id();
            $table->string('front_tag_id', 512)->unique()->index()->comment('前端标签ID');
            $table->string('description', 1024)->nullable()->comment('说明');
            $table->integer('historian_tag_id')->unsigned()->nullable()->comment('Historian Tag ID');
            $table->string('historian_tag_id', 200);
            $table->text('func')->nullable()->comment('计算函数');
            $table->text('color_list')->nullable()->comment('颜色列表');
            $table->string('name', 50)->nullable()->comment('名称');
            $table->string('measure', 50)->nullable()->comment('单位');
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
        Schema::dropIfExists('front_config');
    }
}
