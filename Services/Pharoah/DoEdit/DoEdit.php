<?php

namespace Mid\OutterGameConfigTicket\Services\Pharoah\DoEdit;

use Mid\CommonTools\CommonModel;
use Mid\CommonTools\ReturnTool;
use Mid\CommonTools\Utils\ArrayUtility;
use Mid\Log\Libraries\CommonDTO;
use Mid\OutterGameConfigBasic\Libraries\Pharoah\GameApiVar2Factory;
use Mid\OutterGameConfigTicket\Libraries\Logs\Job;
use Mid\OutterGameConfigTicket\Services\Pharoah\TraitHelper;
use Mid\OutterGameConfigTicket\Services\Pharoah\TraitHelperP109;

abstract class DoEdit
{
    use TraitHelper;

    protected $return_tool;

    protected $log_lib_dto;

    protected $log_job;

    protected $moduleName = 'OutterGameConfigTicket';

    protected $common_model;

    protected $basic_log_lib;

    protected $api_lib;

    protected $game_category = PHALT;

    private $log_detail_salve_insert_data;

    /**
     * @return mixed
     */
    public function getLogDetailSalveInsertData()
    {
        return $this->log_detail_salve_insert_data;
    }

    /**
     * @param mixed $log_detail_salve_insert_data
     */
    public function setLogDetailSalveInsertData($log_detail_salve_insert_data)
    {
        $this->log_detail_salve_insert_data = $log_detail_salve_insert_data;
    }

    function __construct($antGuid)
    {
        $this->return_tool = app(ReturnTool::class);
        $this->log_lib_dto = app(CommonDTO::class);
        $this->log_job = app(Job::class);
        $this->common_model = app(CommonModel::class);
        $this->basic_log_lib = app(\Mid\OutterGameConfigBasic\Libraries\Logs\Job::class);
        $this->api_lib = GameApiVar2Factory::createByGuid(PHALT, $antGuid);
    }

    abstract public function doEdit($request, $result);

    public function commonLayer($request, $result)
    {
        $this->validDoEditPercent($request, TraitHelperP109::getP109AllGameTypeCode());
        $config_detail_collection = \App::make(\Mid\OutterGameConfigBasic\Collection\Detail::class);
        $validate_column = TraitHelperP109::getP109AllGameTypeCode();
        $log_detail_salve_insert_data = [];
        foreach ($validate_column AS $val) {
            $gameCode = constant($val);
            $parent_percent = 0;
            $parent_commission = 0;
            if ($result['data']['parent_account']) {
                /* 取得上線佔成、退水設定 */
                $parent_detail_config = $config_detail_collection->getConfigDetailByP33AntIdP93OgcGameCategoryP93OgcdGameType($result['data']['parent_account']['p33_alr_p33_ant_id'], $this->game_category, $gameCode);
                $parent_percent = ($parent_detail_config['data']) ? $parent_detail_config['data']['p93_ogcd_percent'] : 0;
                $parent_commission = ($parent_detail_config['data']) ? $parent_detail_config['data']['p93_ogcd_commission'] : 0;
                /* END 取得上線佔成、退水設定 */
            }
            if ($result['data']['branch']['p32_bch_type'] == 1) {
                $request[$gameCode]['p93_ogcd_commission'] = 0;
            }
            if ($result['data']['account']['p33_alr_role_code'] != 'ROLE128') {
                /* 非站長及會員需檢查佔成是否大於上線 */
                if ($parent_percent < $request[$gameCode]['p93_ogcd_percent'] && $result['data']['account']['p33_alr_role_code'] != 'ROLE1') {
                    return $this->return_tool->returnMsg(930004,
                        ['text' => ['common' => \Lang::get("Mid\\OutterGameConfigTicket::ticket." . $gameCode) . \Lang::get("Mid\\OutterGameConfigBasic::basic.930004") . '(930004)']],
                        __FILE__, __LINE__);
                }
                /* END 非站長及會員需檢查佔成是否大於上線 */
                if ($parent_commission < $request[$gameCode]['p93_ogcd_commission'] && $result['data']['branch']['p32_bch_type'] == '2') {
                    return $this->return_tool->returnMsg(930005,
                        ['text' => ['common' => \Lang::get("Mid\\OutterGameConfigTicket::ticket." . $gameCode) . \Lang::get("Mid\\OutterGameConfigBasic::basic.930005") . '(930005)']],
                        __FILE__, __LINE__);
                }
            }
            $config_detail = $config_detail_collection->getConfigDetailByP33AntIdP93OgcGameCategoryP93OgcdGameType($result['data']['account']['p33_ant_id'], $this->game_category, $gameCode);
            $insert_data = [
                'p93_ogcd_commission' => ($result['data']['branch']['p32_bch_type'] == 2) ? $request[$gameCode]['p93_ogcd_commission'] : 0,
                'p93_ogcd_percent'    => ($result['data']['account']['p33_alr_role_code'] == 'ROLE1') ? 0 : (($request[$gameCode]['p93_ogcd_percent']) ?? 0),
                'p93_ogcd_game_type'  => $gameCode,
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
                    , $gameCode
                    , $request[$gameCode]['p93_ogcd_commission']
                    , $request[$gameCode]['p93_ogcd_percent']
                );
            }
            /* END 連動下線佔成及退水 */
            /* 設定寫入的LOG資料 */
            $log_detail_salve_insert_data[] = $insert_data;
            /* END 設定寫入的LOG資料 */
        }
        $this->setLogDetailSalveInsertData($log_detail_salve_insert_data);

        return $this->return_tool->returnMsg(0, ['data' => true], __FILE__, __LINE__);
    }

    /**
     * 整理 範本
     * @param $game_template
     * @return array
     */
    protected function coordinateTemplate($game_template)
    {
        $res = [];
        foreach ($game_template AS $val) {
            $res[] = $val['exampleType'];
        }
        $this->checkData($res, 1090002, ['text' => ['common' => \Lang::get("Mid\\CommonTools::errors/common.common") . '(1090002)']]);

        return $res;
    }

    /**
     * 驗證 範本
     * @param $request
     * @param $result
     * @param $game_template
     * @throws \BackEndException
     */
    protected function validDoEdit($request, $result, $game_template)
    {
        $friendly_names = [
            'p109_ogctt_example_type' => \Lang::get("Mid\\OutterGameConfigTicket::ticket.p109_ogctt_example_type")
        ];
        $rules = [
            'p109_ogctt_example_type' => 'required|in:' . ArrayUtility::implode($game_template)
        ];
        $validator = \Validator::make($request, $rules, [], $friendly_names);
        if ($validator->fails()) {
            throw new \BackEndException(300006, ['text' => $validator->messages()->toArray()]);
        }
    }

    /**
     * 驗證 佔成(percent)
     * @param $request
     * @param $gameTypes
     * @return mixed
     * @throws \BackEndException
     */
    protected function validDoEditPercent($request, $gameTypes)
    {
        $rules = [];
        $gameTypes = collect($gameTypes);
        $gameTypes->map(function ($gameType) use (&$rules, &$request) {
            $rules[$gameType] = 'required|array';
            $rules[$gameType . '.p93_ogcd_percent'] = 'required|numeric|min:0|max:100';
            unset($request[$gameType]['p93_ogcd_commission']);
        });
        $validAttrName = $this->getValidAttrName($rules, $this->moduleName);
        $validator = \Validator::make($request, $rules, [], $validAttrName);
        if ($validator->fails()) {
            throw new \BackEndException(300006, ['text' => $validator->messages()->toArray()]);
        }

        return $request;
    }

    /**
     * 驗證 返水(commission)
     * @param $request
     * @param $gameTypes
     * @return mixed
     * @throws \BackEndException
     */
    protected function validDoEditCommission($request, $gameTypes)
    {
        $rules = [];
        $gameTypes = collect($gameTypes);
        $gameTypes->map(function ($gameType) use (&$rules, &$request) {
            $rules[$gameType] = 'required|array';
            $rules[$gameType . '.p93_ogcd_commission'] = 'required|numeric|min:0|max:100';
            unset($request[$gameType]['p93_ogcd_percent']);
        });
        $validAttrName = $this->getValidAttrName($rules, $this->moduleName);
        $validator = \Validator::make($request, $rules, [], $validAttrName);
        if ($validator->fails()) {
            throw new \BackEndException(300006, ['text' => $validator->messages()->toArray()]);
        }

        return $request;
    }

}
