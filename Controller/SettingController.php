<?php

namespace Mid\OutterGameConfigTicket\Controller;

use Mid\OutterGameConfigBasic\Libraries\Pharoah\GameApiVar2Factory;

class SettingController
{
    private $service;

    function __construct()
    {
        $this->service = app(\Mid\OutterGameConfigTicket\Services\Pharoah\Ticket::class, [GameApiVar2Factory::createByGuid(PHALT, request()->get('p33_ant_guid'))]);
    }

    public function show()
    {
        return $this->service->show(\Request::all());
    }

    public function showSelf()
    {
        return $this->service->showSelf();
    }

    public function edit()
    {
        return $this->service->show(\Request::all());
    }

    public function doEdit()
    {
    }
}
