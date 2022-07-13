<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPeriodToOfflineDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offline_data', function (Blueprint $table) {
            $table->enum('period', ['date', 'month', 'year'])->nullable()->comment('记录周期');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offline_data', function (Blueprint $table) {
            $table->dropColumn('period');
        });
    }
}
