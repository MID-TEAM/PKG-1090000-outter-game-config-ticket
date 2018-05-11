<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutterGameConfigTicketTemplateTable extends Migration
{
    /**
     * 外部遊戲設定.佔成退水
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p109_outter_game_config_ticket_template', function (Blueprint $table) {
            $table->increments('p109_ogctt_id');
            $table->char('p109_ogctt_guid', 36);
            $table->integer('p109_ogctt_add_date')->comment('建立日期');
            $table->integer('p109_ogctt_udate')->comment('更新日期');
            $table->tinyInteger('p109_ogctt_status')->default(3)->comment('狀態 (3.啟用 -2.停用 -3.刪除)');
            $table->string('p109_ogctt_example_type')->comment('彩票範本');
            $table->unsignedInteger('p109_ogctt_p93_ogc_id')->comment('外部遊戲設定 ID');
            /* index */
            $table->unique('p109_ogctt_guid', 'idx_p109_ogctt_guid');
            $table->index('p109_ogctt_p93_ogc_id', 'idx_p109_ogctt_p93_ogc_id');
            /* END index */
            /* foreign */
            $table->foreign('p109_ogctt_p93_ogc_id', 'fk_p109_ogctt_p93_ogc_id')->references('p93_ogc_id')->on('p93_outter_game_config')->onDelete('cascade')->onUpdate('cascade');
            /* END foreign */
        });
        DB::statement("ALTER TABLE `p109_outter_game_config_ticket_template` comment '彩票範本'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::drop('p109_outter_game_config_ticket_template');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
