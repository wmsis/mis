<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentMaintenanceRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_maintenance_record', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->date('date')->useCurrent()->index()->comment('检修日期');
            $table->string('kind', 512)->nullable()->index()->comment('检修性质');
            $table->string('supervisor', 128)->nullable()->index()->comment('负责人');
            $table->string('evaluation', 1024)->nullable()->comment('验收评价');
            $table->string('members', 1024)->nullable()->comment('工作组成员');
            $table->text('prev_status')->nullable()->comment('检修前状况');
            $table->text('maintenance_content')->nullable()->comment('检修内容');
            $table->text('test_status')->nullable()->comment('试运情况');
            $table->text('after_status')->nullable()->comment('检修后状况');
            $table->string('recorder', 128)->nullable()->comment('记录人');
            $table->timestamp('record_time')->useCurrent()->comment('记录时间');
            $table->foreignId('equipment_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipment_maintenance_record');
    }
}
