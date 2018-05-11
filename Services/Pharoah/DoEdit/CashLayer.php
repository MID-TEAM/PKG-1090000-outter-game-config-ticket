<?php

namespace Mid\OutterGameConfigTicket\Services\Pharoah\DoEdit;

class CashLayer extends DoEdit
{
    public function doEdit($request, $result)
    {
        return $this->commonLayer($request, $result);
    }

    public function doEditLog($request, $result)
    {
        $this->log_lib_dto->p32_bch_id = $result['data']['branch']['p32_bch_id'];
        $this->log_lib_dto->p33_ant_id = $result['data']['account']['p33_ant_id'];
        $this->log_lib_dto->p33_ant_id_creator = config('Initial.global.account.p33_ant_id');
        $this->log_lib_dto->log_memo = '修改彩票設定(現金階層)';
        $this->log_lib_dto->ip = config('Initial.global.ip');
        $this->log_lib_dto->time = time();
        $basic_log_lib = \App::make(\Mid\OutterGameConfigBasic\Libraries\Logs\Job::class);
        $log_detail_salve_insert_data = $this->getLogDetailSalveInsertData();
        $this->log_job
            ->setSlaveInsertData($basic_log_lib->getSlaveInsertData())
            ->setSlaveDetailInsertData($log_detail_salve_insert_data)
            ->setTemplateInsertData([])
            ->sendJob($this->log_lib_dto);
    }
}
