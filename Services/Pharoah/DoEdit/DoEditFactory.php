<?php

namespace Mid\OutterGameConfigTicket\Services\Pharoah\DoEdit;

class DoEditFactory
{
    /**
     * @param string $type
     * @param string $antGuid
     * @return mixed
     * @throws \BackEndException
     */
    public static function create(string $type, string $antGuid)
    {
        try {
            $className = "Mid\\OutterGameConfigTicket\\Services\\Pharoah\\DoEdit\\" . $type;

            return new $className($antGuid);
        } catch (\Exception $e) {
            throw new \BackEndException('9999', ['建立資源失敗']);
        }
    }
}
