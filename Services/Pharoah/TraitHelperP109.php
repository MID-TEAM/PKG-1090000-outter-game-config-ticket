<?php

namespace Mid\OutterGameConfigTicket\Services\Pharoah;

trait TraitHelperP109
{
    /**
     * 取得gameType資訊
     * @param $gameType
     * @return mixed
     */
    public static function getP109GameType($gameType)
    {
        return config('OutterGameConfigTicket.constant.gameType.' . $gameType);
    }

    /**
     * 取得所有gameType資訊列表
     * @return array
     */
    public static function getP109AllGameType()
    {
        $all = config('OutterGameConfigTicket.constant.gameType');
        $res = collect($all)->keys()->toArray();

        return $res;
    }

    /**
     * 取得所有gameType code資訊列表
     * @return array
     */
    public static function getP109AllGameTypeCode()
    {
        $all = config('OutterGameConfigTicket.constant.gameType');
        $res = collect($all)->map(function ($item) {
            return $item['code'];
        })->toArray();

        return array_values($res);
    }

    /**
     * 取得對應的gameType code資訊
     * @param $gameType
     * @return mixed
     */
    public static function getP109GameTypeCode($gameType)
    {
        return config('OutterGameConfigTicket.constant.gameType.' . $gameType . '.code');
    }

}
