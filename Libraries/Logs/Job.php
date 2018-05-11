<?php

namespace Mid\OutterGameConfigTicket\Libraries\Logs;

use Mid\OutterGameConfigBasic\Libraries\Logs AS OutterGameConfigBasicLog;
use \App\Jobs\Logs\OutterGameConfig AS OutterGameConfigJob;

class Job extends OutterGameConfigBasicLog\Job
{

    private $template_insert_data;

    public function setTemplateInsertData(array $insert_data = [])
    {
        $this->template_insert_data = $insert_data;

        return $this;
    }

    public function sendJob(\Mid\Log\Libraries\CommonDTO $commonDTO)
    {
        $job = (new OutterGameConfigJob\Ticket($commonDTO, $this->slave_insert_data, $this->slave_detail_insert_data, $this->template_insert_data))->onQueue('outterGameConfigTicketLog');
        /* 送到 queue */
        dispatch($job);
        /* END送到 queue */
    }
}