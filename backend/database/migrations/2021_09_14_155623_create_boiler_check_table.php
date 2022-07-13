<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoilerCheckTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boiler_check', function (Blueprint $table) {
            $table->id();
            $table->integer('module_id')->nullable()->comment('模块ID');
            $table->text('tag_en_name')->nullable()->comment('tag英文名，多个用英文逗号分开');
            $table->string('tag_cn_name', 250)->nullable()->comment('考核中文名');
            $table->string('func', 250)->nullable()->comment('函数');
            $table->string('upper_limit', 50)->nullable()->comment('报警上限');
            $table->string('lower_limit', 50)->nullable()->comment('报警下限');
            $table->string('time_limit', 50)->nullable()->comment('报警时限');
            $table->integer('sort')->nullable()->comment('排序号');
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
        Schema::dropIfExists('boiler_check');
    }
}
