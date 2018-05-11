<?php

namespace Mid\OutterGameConfigTicket\Services\Pharoah\DoEdit;

class CreditLayer extends DoEdit
{
    public function doEdit($request, $result)
    {
        return $this->commonLayer($request, $result);
    }

    public function doEditApi($request, $result)
    {
    }

    public function doEditLog($request, $result)
    {
    }
}
