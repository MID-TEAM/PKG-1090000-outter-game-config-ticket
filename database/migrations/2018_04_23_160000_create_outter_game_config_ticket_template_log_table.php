<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutterGameConfigTicketTemplateLogTable extends Migration
{
    /**
     * 外部遊戲設定.佔成退水
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(env('DB_BRANCH_LOG_CONNECTION', 'mysql'))->create('p109_outter_game_config_ticket_template_log', function (Blueprint $table) {
            $table->increments('p109_ogcttl_id');
            $table->string('p109_ogcttl_example_type')->comment('彩票範本');
            $table->unsignedInteger('p109_ogcttl_p93_ogcsl_id')->comment('外部遊戲設定 ID');
            /* index */
            $table->index('p109_ogcttl_p93_ogcsl_id', 'idx_p109_ogcttl_p93_ogcsl_id');
            /* END index */
            /* foreign */
            $table->foreign('p109_ogcttl_p93_ogcsl_id', 'fk_p109_ogcttl_p93_ogcsl_id')->references('p93_ogcsl_id')->on('p93_outter_game_config_slave_log')->onDelete('cascade')->onUpdate('cascade');
            /* END foreign */
        });
        DB::connection(env('DB_BRANCH_LOG_CONNECTION', 'mysql'))->statement("ALTER TABLE `p109_outter_game_config_ticket_template_log` comment '彩票範本LOG'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection(env('DB_BRANCH_LOG_CONNECTION', 'mysql'))->statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::connection(env('DB_BRANCH_LOG_CONNECTION', 'mysql'))->drop('p109_outter_game_config_ticket_template_log');
        DB::connection(env('DB_BRANCH_LOG_CONNECTION', 'mysql'))->statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
