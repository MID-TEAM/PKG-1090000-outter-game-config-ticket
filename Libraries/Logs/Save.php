<?php

namespace Mid\OutterGameConfigTicket\Libraries\Logs;

use \Mid\OutterGameConfigBasic\Libraries\Logs;
use Mid\SportBase\Helpers\UuidHelper;

/**
 *  儲存log
 */
class Save extends Logs\Save
{
    use UuidHelper;

    /**
     * 範本
     */
    public $table_template = 'p109_outter_game_config_ticket_template_log';

    public function doSaveTemplate(array $insert_data = [])
    {
        if ($insert_data) {
            $insert = [];
            foreach ($insert_data AS $val) {
                $insert[] = [
                    'p109_ogcttl_example_type' => $val['p109_ogctt_example_type'],
                    'p109_ogcttl_p93_ogcsl_id' => $this->slave_id
                ];
            }
            \DB::connection(env('DB_BRANCH_LOG_CONNECTION'))->table($this->table_template)->insert($insert);
        }

        return $this;
    }

}