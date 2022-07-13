<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOriginLimitsToHistorianTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('historian_tag', function (Blueprint $table) {
            $table->float('origin_upper_limit')->nullable()->comment('原始上限值, historian记录的量程上限');
            $table->float('origin_lower_limit')->nullable()->comment('原始下限值, historian记录的量程下限');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historian_tag', function (Blueprint $table) {
            $table->dropColumn('origin_upper_limit');
            $table->dropColumn('origin_lower_limit');
        });
    }
}
