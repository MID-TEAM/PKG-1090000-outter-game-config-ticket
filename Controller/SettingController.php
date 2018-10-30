<?php

namespace Mid\OutterGameConfigTicket\Controller;

use Mid\OutterGameConfigBasic\Libraries\Pharoah\GameApiVar2Factory;

class SettingController
{
    private $service;

    function __construct()
    {
        if (request()->has('p33_ant_guid') &&  request()->get('p33_ant_guid') != null) {
            $this->service = app(\Mid\OutterGameConfigTicket\Services\Pharoah\Ticket::class, [GameApiVar2Factory::createByGuid(PHALT, request()->get('p33_ant_guid'))]);
        } else {
            $member = config('Initial.global.account');
            $this->service = app(\Mid\OutterGameConfigTicket\Services\Pharoah\Ticket::class, [GameApiVar2Factory::create(PHALT, $member['p33_ant_id'], $member['p33_ant_p32_bch_id'])]);
        }
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
