<?php

namespace Mid\OutterGameConfigTicket\Services\Pharoah;

use Mid\OutterGameConfigBasic\Services AS OutterGameConfigService;
use Mid\OutterGameConfigTicket\Libraries AS OutterGameConfigTicketLibraries;
use Mid\OutterGameConfigTicket\Services\Pharoah\DoEdit\DoEditFactory;

class Ticket extends OutterGameConfigService\Basic
{
    private $pharaoh_api;

    private $return_tool;

    protected $game_category = PHALT;

    private $log_lib_dto;

    private $log_job;

    function __construct(OutterGameConfigTicketLibraries\Pharoah\ApiVar2 $pharaoh_api)
    {
        parent::__construct();
        $this->game_category = PHALT;
        $this->pharaoh_api = $pharaoh_api;
        $this->return_tool = \App::make(\Mid\CommonTools\ReturnTool::class);
        $this->log_lib_dto = \App::make(\Mid\Log\Libraries\CommonDTO::class);
        $this->log_job = \App::make(OutterGameConfigTicketLibraries\Logs\Job::class);
    }

    /**
     * @param array $request
     * @return array
     */
    public function show(array $request): array
    {
        $check = $this->checkOperator($request);
        if ($check['code']) {
            return $check;
        }
        /* 取得帳號資料 */
        $account = \App::make(\Mid\Account\Collection\Common::class)->getAccountBranchByP33AntGuid($request['p33_ant_guid']);
        /* END 取得帳號資料 */
        /* 取得設定 */
        $config = \App::make(\Mid\OutterGameConfigBasic\Collection\Basic::class)->getConfigBYP33AntIdP93OgcGameCategory($account['data']['p33_ant_id'], $this->game_category);
        /* END 取得設定 */
        $point = 0;
        $parent_config = null;
        $game_template = [];
        $layer = \App::make(\Mid\Account\Collection\Common::class)->getAccountLayerByP33AntGuid($account['data']['p33_ant_guid'], [-3, -2, -1]);
        $parent_config = $this->buildParentConfig(
            $layer['data']['p33_alr_parent_p33_ant_id'],
            $layer['data']['p33_alr_role_code'],
            $config['data']['p93_ogc_quota'],
            $this->game_category);
        if ($layer['data']['p33_alr_role_code'] == 'ROLE1') {
            /* 取得遠端帳號 */
            $remote_account = $this->pharaoh_api->checkCreateAccount();
            if ($remote_account['code']) {
                return $this->return_tool->returnMsg($remote_account['code'], ['data' => $remote_account['data']], __FILE__, __LINE__);
            }
            /* END 取得遠端帳號 */
            /* 取得點數、額度 */
            if ($remote_account['data']) {
                $point = $this->pharaoh_api->accountPoint();
                $point = $point['data']['credit'];
            }
            /* END 取得點數、額度 */
        }
        /* 取得範本 */
        $game_template = $this->pharaoh_api->stakeTemplate();
        $game_template = $game_template['data'];
        /* END 取得範本 */
        $return_data = [
            'config'        => $config['data'],
            'point'         => $point,
            'parent_config' => $parent_config,
            'game_template' => $game_template
        ];
        if ($account['data']['p32_bch_type'] == 1) {
            $return_data['current_point'] = $point;
        }
        if ($account['data']['p32_bch_type'] == 2) {
            if ($layer['data']['p33_alr_role_code'] == 'ROLE1') {
                $return_data['current_quota'] = $point;
            } else {
                $return_data['current_quota'] = $return_data['parent_config']['p93_ogc_quota'] - $return_data['config']['p93_ogc_quota'];
            }
        }

        return $this->return_tool->returnMsg(0, ['data' => $return_data], __FILE__, __LINE__);
    }

    /**
     * @return array
     */
    public function showSelf(): array
    {
        /* 取得帳號資料 */
        $antId = (!empty(config('Initial.global.account.p33_ant_dependency_p33_ant_id'))) ? config('Initial.global.account.p33_ant_dependency_p33_ant_id') : config('Initial.global.account.p33_ant_id');
        $account = \App::make(\Mid\Account\Collection\Common::class)->getAccountBranchByP33AntId($antId);
        /* END 取得帳號資料 */
        /* 取得設定 */
        $config = \App::make(\Mid\OutterGameConfigBasic\Collection\Basic::class)->getConfigBYP33AntIdP93OgcGameCategory($account['data']['p33_ant_id'], $this->game_category);
        /* END 取得設定 */
        $point = 0;
        $parent_config = null;
        $game_template = [];
        $layer = \App::make(\Mid\Account\Collection\Common::class)->getAccountLayerByP33AntGuid($account['data']['p33_ant_guid'], [-3, -2, -1]);
        if ($layer['data']['p33_alr_parent_p33_ant_id']) {
            $parent_config = \App::make(\Mid\OutterGameConfigBasic\Collection\Basic::class)->getConfigBYP33AntIdP93OgcGameCategory($layer['data']['p33_alr_parent_p33_ant_id'], $this->game_category);
            $parent_config = $parent_config['data'];
        }
        if ($layer['data']['p33_alr_role_code'] == 'ROLE1') {
            /* 取得遠端帳號 */
            $remote_account = $this->pharaoh_api->checkCreateAccount();
            if ($remote_account['code']) {
                return $this->return_tool->returnMsg($remote_account['code'], ['data' => $remote_account['data']], __FILE__, __LINE__);
            }
            /* END 取得遠端帳號 */
            /* 取得點數、額度 */
            if ($remote_account['data']) {
                $point = $this->pharaoh_api->accountPoint();
                $point = $point['data']['credit'];
            }
            /* END 取得點數、額度 */
        }
        /* 取得範本 */
        $game_template = $this->pharaoh_api->stakeTemplate();
        $game_template = $game_template['data'];
        /* END 取得範本 */
        $return_data = [
            'config'        => $config['data'],
            'point'         => $point,
            'parent_config' => $parent_config,
            'game_template' => $game_template
        ];

        return $this->return_tool->returnMsg(0, ['data' => $return_data], __FILE__, __LINE__);
    }

    /**
     * @param array $request
     * @return array
     */
    public function doEdit(array $request): array
    {
        \DB::beginTransaction();
        $result = parent::doEdit($request);
        if ($result['code']) {
            return $result;
        }
        $roleCode = $result['data']['account']['p33_alr_role_code'];
        $bchType = $result['data']['branch']['p32_bch_type'];
        $editType = $this->defineType($bchType);
        $editType .= $this->defineRoleCode($roleCode);
        $doEditBehavior = app(DoEditFactory::class)->create($editType, $request['p33_ant_guid']);
        $res = $doEditBehavior->doEdit($request, $result);
        if ($res['code'] != 0) {
            return $res;
        }
        $doEditBehavior->doEditLog($request, $result);
        \DB::commit();

        return $this->return_tool->returnMsg(0, ['data' => true], __FILE__, __LINE__);
    }

    /**
     * 回復會員限額
     * 1. 是否為上線於呼叫前先行判斷
     * 2.  站台是否有啟用該項遊戲於呼叫前先行判斷
     * 3.  須帶入站台 GUID(呼叫端自行確認站台存在)
     * 3.  須帶入帳號 GUID(呼叫端自行確認帳號存在)
     *
     * @param array $request
     * @return array
     */
    public function recoverLimitQouta(array $request): array
    {
        return $this->pharaoh_api->recoverWinLose();
    }

    /**
     * 定義 現金 還是 信用
     * @param $bchType
     * @return string
     * @throws \BackEndException
     */
    private function defineType($bchType)
    {
        if ($bchType == '1') {
            return 'Cash';
        } elseif ($bchType == '2') {
            return 'Credit';
        } else {
            throw new \BackEndException('9999', ['分類資源失敗']);
        }
    }

    /**
     * 定義 roleCode
     * @param $roleCode
     * @return string
     */
    private function defineRoleCode($roleCode)
    {
        if ($roleCode == 'ROLE1') {
            return 'Player';
        } else {
            return 'Layer';
        }
    }
}
