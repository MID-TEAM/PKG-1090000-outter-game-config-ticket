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
            ],
            '20005' => [
                'code' => 'WELFARE3D',
                'name' => 'welfare_3d',
                'chi'  => '福彩3D',
            ],
            '10017' => [
                'code' => 'LUCKYAIRSHIP',
                'name' => 'lucky_airship',
                'chi'  => '幸運飛艇',
            ],
            '30001' => [
                'code' => 'GUANGDONG',
                'name' => 'guang_dong',
                'chi'  => '廣東11選5',
            ],
            '30004' => [
                'code' => 'XINJIANG',
                'name' => 'xin_jiang',
                'chi'  => '新疆11選5',
            ],
            '30005' => [
                'code' => 'SHANGHAI',
                'name' => 'shang_hai',
                'chi'  => '上海11選5',
            ],
            '30008' => [
                'code' => 'SHANDONG',
                'name' => 'shan_dong',
                'chi'  => '山東11選5',
            ],
            '30015' => [
                'code' => 'JIANGXI',
                'name' => 'jiang_xi',
                'chi'  => '江西11選5',
            ],
            '40002' => [
                'code' => 'BEIJINGF3',
                'name' => 'beijing_f3',
                'chi'  => '北京快3',
            ],
            '40007' => [
                'code' => 'JIANGSUF3',
                'name' => 'jiangsu_f3',
                'chi'  => '江蘇快3',
            ],
            '40010' => [
                'code' => 'GUANGXIF3',
                'name' => 'guangxi_f3',
                'chi'  => '廣西快3',
            ],
            '40012' => [
                'code' => 'JILINF3',
                'name' => 'jilin_f3',
                'chi'  => '吉林快3',
            ],
            '40017' => [
                'code' => 'HENANF3',
                'name' => 'hanan_f3',
                'chi'  => '河南快3',
            ],
            '10006' => [
                'code' => 'AUSTRALIA5',
                'name' => 'australia_5',
                'chi'  => '澳洲幸運5',
            ],
            '10018' => [
                'code' => 'SGAIRSHIP',
                'name' => 'sg_airship',
                'chi'  => 'SG飛艇',
            ],
            '10019' => [
                'code' => 'AUSTRALIA10',
                'name' => 'australia_10',
                'chi'  => '澳洲幸運10',
            ],
        ];
    }
}
