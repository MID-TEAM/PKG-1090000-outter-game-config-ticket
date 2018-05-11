<?php

namespace Mid\OutterGameConfigTicket\Services\Pharoah\DoEdit;

use Mid\OutterGameConfigTicket\Services\Pharoah\TraitHelperP109;

class CreditPlayer extends DoEdit
{
    public function doEdit($request, $result)
    {
        /* 範本 */
        $game_template = $this->api_lib->stakeTemplate()['data'] ?? null;
        $this->checkData($game_template, 1090002, ['text' => ['common' => \Lang::get("Mid\\CommonTools::errors/common.common") . '(1090002)']]);
        $game_template = $this->coordinateTemplate($game_template); //整理範本
        /* END 範本 */
        $this->validDoEdit($request, $result, $game_template);
        $this->validDoEditCommission($request, TraitHelperP109::getP109AllGameTypeCode());
        $validate_column = TraitHelperP109::getP109AllGameTypeCode();
        //刪除原範本
        \DB::table('p109_outter_game_config_ticket_template')->where('p109_ogctt_p93_ogc_id', $result['data']['account_config']['p93_ogc_id'])->delete();
        //寫入新範本
        $insert_data = [
            'p109_ogctt_example_type' => $request['p109_ogctt_example_type'],
            'p109_ogctt_p93_ogc_id'   => $result['data']['account_config']['p93_ogc_id']
        ];
        $this->common_model->insert('p109_outter_game_config_ticket_template', 'p109_ogctt', $insert_data);
        $log_template_insert_data[] = $insert_data;
        $config_detail_collection = \App::make(\Mid\OutterGameConfigBasic\Collection\Detail::class);
        foreach ($validate_column AS $val) {
            $parent_percent = 0;
            $parent_commission = 0;
            if ($result['data']['parent_account']) {
                /* 取得上線佔成、退水設定 */
                $parent_detail_config = $config_detail_collection->getConfigDetailByP33AntIdP93OgcGameCategoryP93OgcdGameType($result['data']['parent_account']['p33_alr_p33_ant_id'], $this->game_category, $val);
                $parent_percent = ($parent_detail_config['data']) ? $parent_detail_config['data']['p93_ogcd_percent'] : 0;
                $parent_commission = ($parent_detail_config['data']) ? $parent_detail_config['data']['p93_ogcd_commission'] : 0;
                /* END 取得上線佔成、退水設定 */
            }
            if ($result['data']['account']['p33_alr_role_code'] != 'ROLE128') {
                /* 非站長及會員需檢查佔成是否大於上線 */
                if ($parent_percent < $request[$val]['p93_ogcd_percent'] && $result['data']['account']['p33_alr_role_code'] != 'ROLE1') {
                    return $this->return_tool->returnMsg(930004,
                        ['text' => ['common' => \Lang::get("Mid\\OutterGameConfigTicket::ticket." . $val) . \Lang::get("Mid\\OutterGameConfigBasic::basic.930004") . '(930004)']],
                        __FILE__, __LINE__);
                }
                /* END 非站長及會員需檢查佔成是否大於上線 */
                if ($parent_commission < $request[$val]['p93_ogcd_commission'] && $result['data']['branch']['p32_bch_type'] == '2') {
                    return $this->return_tool->returnMsg(930005,
                        ['text' => ['common' => \Lang::get("Mid\\OutterGameConfigTicket::ticket." . $val) . \Lang::get("Mid\\OutterGameConfigBasic::basic.930005") . '(930005)']],
                        __FILE__, __LINE__);
                }
            }
            $config_detail = $config_detail_collection->getConfigDetailByP33AntIdP93OgcGameCategoryP93OgcdGameType($result['data']['account']['p33_ant_id'], $this->game_category, $val);
            $insert_data = [
                'p93_ogcd_commission' => ($result['data']['branch']['p32_bch_type'] == 2) ? $request[$val]['p93_ogcd_commission'] : 0,
                'p93_ogcd_percent'    => ($result['data']['account']['p33_alr_role_code'] == 'ROLE1') ? 0 : (($request[$val]['p93_ogcd_percent']) ?? 0),
                'p93_ogcd_game_type'  => $val,
                'p93_ogcd_p93_ogc_id' => $result['data']['account_config']['p93_ogc_id']
            ];
            if (!$config_detail['data']) {
                $this->common_model->insert('p93_outter_game_config_detail', 'p93_ogcd', $insert_data);
            } else {
                $this->common_model->update('p93_outter_game_config_detail', 'p93_ogcd', $config_detail['data']['p93_ogcd_guid'], $insert_data);
            }
            /* 連動下線佔成及退水 */
            if ($result['data']['account']['p33_alr_role_code'] != 'ROLE1') {
                \App::make(\Mid\OutterGameConfigBasic\Libraries\Detail::class)->duplicateChangeParent(
                    constant($result['data']['account']['p33_alr_role_code'])
                    , $result['data']['account']['p33_ant_id']
                    , $this->game_category
                    , $val
                    , $request[$val]['p93_ogcd_commission']
                    , $request[$val]['p93_ogcd_percent']
                );
            }
        }

        return $this->return_tool->returnMsg(0, ['data' => true], __FILE__, __LINE__);
    }

    public function doEditApi($request, $result)
    {
    }

    public function doEditLog($request, $result)
    {
    }
}
