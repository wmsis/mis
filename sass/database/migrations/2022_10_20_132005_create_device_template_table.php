<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_template', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable()->comment('设备模板名');
            $table->integer('parent_id')->nullable()->comment('父节点ID');
            $table->integer('ancestor_id')->nullable()->comment('祖先节点ID');
            $table->integer('level')->nullable()->comment('所属层级');
            $table->integer('sort')->nullable()->comment('排序号');
            $table->integer('is_group')->nullable()->comment('是否分组');
            $table->integer('orgnization_id')->nullable()->comment('组织ID');

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
        Schema::dropIfExists('device_template');
    }
}
