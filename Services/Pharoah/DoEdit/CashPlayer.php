<?php

namespace Mid\OutterGameConfigTicket\Services\Pharoah\DoEdit;

class CashPlayer extends DoEdit
{
    private $log_template_insert_data;

    /**
     * @return mixed
     */
    public function getLogTemplateInsertData()
    {
        return $this->log_template_insert_data;
    }

    /**
     * @param mixed $log_template_insert_data
     */
    public function setLogTemplateInsertData($log_template_insert_data)
    {
        $this->log_template_insert_data = $log_template_insert_data;
    }

    public function doEdit($request, $result)
    {
        $game_template = $this->api_lib->stakeTemplate()['data'] ?? null;
        $this->checkData($game_template, 1090002, ['text' => ['common' => \Lang::get("Mid\\CommonTools::errors/common.common") . '(1090002)']]);
        $game_template = $this->coordinateTemplate($game_template); //整理範本
        $this->validDoEdit($request, $result, $game_template);
        $this->doEditApi($request, $result);
        $log_template_insert_data[] = $this->updateTemplate($request, $result);
    }

    /**
     * 更新範本
     * @param $request
     * @param $result
     * @return array
     */
    public function updateTemplate($request, $result)
    {
        //刪除原範本
        \DB::table('p109_outter_game_config_ticket_template')->where('p109_ogctt_p93_ogc_id', $result['data']['account_config']['p93_ogc_id'])->delete();
        //寫入新範本
        $insert_data = [
            'p109_ogctt_example_type' => $request['p109_ogctt_example_type'],
            'p109_ogctt_p93_ogc_id'   => $result['data']['account_config']['p93_ogc_id']
        ];
        $this->common_model->insert('p109_outter_game_config_ticket_template', 'p109_ogctt', $insert_data);
        $this->setLogTemplateInsertData($insert_data);

        return $insert_data;
    }

    /**
     * 送出設定到遊戲商
     * @param $request
     * @param $result
     */
    private function doEditApi($request, $result)
    {
        $this->api_lib->checkCreateAccount();
        $this->api_lib->assignWinLoseLimit($request['p93_ogc_win_max_limit'], $request['p93_ogc_lose_max_limit']);
        $this->api_lib->assignCashTemplate($request['p109_ogctt_example_type']);
        $this->api_lib->assignAccountsMode($request['p93_ogc_bet_status']);
    }

    /**
     * 紀錄 LOG
     * @param $request
     * @param $result
     */
    public function doEditLog($request, $result)
    {
        $this->log_lib_dto->p32_bch_id = $result['data']['branch']['p32_bch_id'];
        $this->log_lib_dto->p33_ant_id = $result['data']['account']['p33_ant_id'];
        $this->log_lib_dto->p33_ant_id_creator = config('Initial.global.account.p33_ant_id');
        $this->log_lib_dto->log_memo = '修改彩票設定(現金會員)';
        $this->log_lib_dto->ip = config('Initial.global.ip');
        $this->log_lib_dto->time = time();
        $log_template_insert_data[] = $this->getLogTemplateInsertData();
        $this->log_job
            ->setSlaveInsertData($this->basic_log_lib->getSlaveInsertData())
            ->setSlaveDetailInsertData()
            ->setTemplateInsertData($log_template_insert_data)
            ->sendJob($this->log_lib_dto);
    }
}
