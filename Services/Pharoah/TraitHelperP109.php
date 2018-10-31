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
        return self::getTicketGameTypeInformation()[$gameType];
    }

    /**
     * 取得所有gameType資訊列表
     * @return array
     */
    public static function getP109AllGameType()
    {
        $all = self::getTicketGameTypeInformation();
        $res = collect($all)->keys()->toArray();

        return $res;
    }

    /**
     * 取得所有gameType code資訊列表
     * @return array
     */
    public static function getP109AllGameTypeCode()
    {
        $all = self::getTicketGameTypeInformation();
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
        return self::getTicketGameTypeInformation()[$gameType]['code'];
    }

    /**
     * 取得彩票遊戲資訊 (同 config / constant.php)
     *
     * @return array
     */
    public static function getTicketGameTypeInformation()
    {
        return [
            '10003' => [
                'code' => 'TJLOTTO',
                'name' => 'tj_lottery',
                'chi'  => '天津時時彩',
            ],
            '10004' => [
                'code' => 'XJLOTTO',
                'name' => 'xj_lottery',
                'chi'  => '新疆時時彩',
            ],
            '10011' => [
                'code' => 'CQLOTTO',
                'name' => 'cq_lottery',
                'chi'  => '重慶時時彩',
            ],
            '10016' => [
                'code' => 'BJPK10',
                'name' => 'bj_pk10',
                'chi'  => '北京PK10',
            ],
            '20001' => [
                'code' => 'BINGO',
                'name' => 'bingo',
                'chi'  => '賓果賓果',
            ],
            '20002' => [
                'code' => 'BJHAPPY8',
                'name' => 'bj_happy8',
                'chi'  => '北京快樂8',
            ],
            '88889' => [
                'code' => 'SPEEDCAR',
                'name' => 'sp_car',
                'chi'  => '極速賽車',
            ]
        ];
    }
}
