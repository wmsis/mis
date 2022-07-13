<?php

use App\Http\Models\SIS\EnvMonitorConfig;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnvMonitorConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('env_monitor_config', function (Blueprint $table) {
            $table->id();
            $table->string('url', 512)->comment('http://www.envsc.cn/ API地址');
            $table->string('token', 128)->nullable()->comment('http://www.envsc.cn/ 服务授权码');
            $table->string('phone_number', 32)->nullable()->comment('登录电话号码');
            $table->string('password', 128)->nullable()->comment('登录密码');
            $table->timestamps();
        });
        EnvMonitorConfig::create(['url' => 'http://xxgk.envsc.cn/ljfspsinfo/EnterpriseData/']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('env_monitor_config');
    }
}
