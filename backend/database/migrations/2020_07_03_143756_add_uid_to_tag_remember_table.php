<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUidToTagRememberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tag_remember', function (Blueprint $table) {
            $table->integer('uid')->nullable()->comment('记住选择的用户id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tag_remember', function (Blueprint $table) {
            $table->dropColumn('uid');
        });
    }
}
