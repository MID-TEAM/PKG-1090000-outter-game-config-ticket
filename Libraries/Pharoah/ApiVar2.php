<?php

namespace Mid\OutterGameConfigTicket\Libraries\Pharoah;

use Mid\BetCasinoApi\Service\Game\PhaLottery\Dto\Request\LotteryCreditLimitInfoRequestDTO;
use Mid\BetCasinoApi\Service\Game\PhaLottery\Dto\Request\LotteryGameUrlRequestDTO;
use Mid\BetCasinoApi\Service\Game\PhaLottery\Dto\Request\LotteryKickUserRequestDTO;
use Mid\BetCasinoApi\Service\Game\PhaLottery\Dto\Request\LotteryPlayerRegisterRequestDTO;
use Mid\BetCasinoApi\Service\Game\PhaLottery\Dto\Request\LotteryResetUserLimitRequestDTO;
use Mid\BetCasinoApi\Service\Game\PhaLottery\Dto\Request\LotterySetCreditResetGroupRequestDTO;
use Mid\BetCasinoApi\Service\Game\PhaLottery\Dto\Request\LotterySetUserCreditRequestDTO;
use Mid\BetCasinoApi\Service\Game\PhaLottery\Dto\Request\LotterySetUserModeRequestDTO;
use Mid\BetCasinoApi\Service\Game\PhaLottery\Dto\Request\LotterySetUserRefundRequestDTO;
use Mid\BetCasinoApi\Service\Game\PhaLottery\Dto\Request\LotterySetUserStakeLimitRequestDTO;
use Mid\BetCasinoApi\Service\Game\PhaLottery\Dto\Request\LotteryStakeLimitRequestDTO;
use Mid\BetCasinoApi\Service\Game\PhaLottery\Dto\Request\LotteryTransferPointRequestDTO;
use Mid\BetCasinoApi\Service\Game\PhaLottery\Dto\Request\LotteryUserHoldingRequestDTO;
use Mid\BetCasinoApi\Service\Game\PhaLottery\Dto\Request\LotteryUserInfoRequestDTO;
use Mid\BetCasinoApi\Service\Game\PhaLottery\Dto\Request\LotteryUserLimitInfoRequestDTO;
use Mid\BetCasinoApi\Service\Game\PhaLottery\Dto\Request\LotteryUserLoseLimitRequestDTO;
use Mid\BetCasinoApi\Service\Game\PhaLottery\Dto\Request\LotteryUserModifyNickNameRequestDTO;
use Mid\BetCasinoApi\Service\Game\PhaLottery\Dto\Request\LotteryUserWinLimitRequestDTO;
use Mid\CommonTools\Utils\ArrayUtility;
use Mid\CommonTools\Utils\JsonUtility;
use Mid\OutterGameConfigBasic\Libraries\Pharoah\IP93GameApiVar2;
use Mid\OutterGameConfigBasic\Libraries\Pharoah\P93GameApiVar2;
use Mid\PlatformOrder\Service\Order\p77PlatformOrder;

class ApiVar2 extends P93GameApiVar2 implements IP93GameApiVar2
{
    /**
     * @var string
     */
    private $lotteryType = '';

    /**
     * @var int
     */
    private $limitWin = 0;

    /**
     * @var int
     */
    private $limitLose = 0;

    /**
     * @return mixed
     */
    public function getLotteryType()
    {
        return $this->lotteryType;
    }

    /**
     * @param $lotteryType
     * @return $this
     */
    public function setLotteryType($lotteryType)
    {
        $this->lotteryType = $lotteryType;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimitWin(): int
    {
        return $this->limitWin;
    }

    /**
     * @param int $limitWin
     * @return $this
     */
    public function setLimitWin(int $limitWin)
    {
        $this->limitWin = $limitWin;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimitLose(): int
    {
        return $this->limitLose;
    }

    /**
     * @param int $limitLose
     * @return $this
     */
    public function setLimitLose(int $limitLose)
    {
        $this->limitLose = $limitLose;

        return $this;
    }

    /**
     * 加扣點
     * @param p77PlatformOrder $order
     * @return mixed
     */
    public function transferPoint(p77PlatformOrder $order)
    {
        $sendData = [
            'account' => $this->account,
            'credit'  => $order->getP77PorOrderPoint(),
            'orderNo' => $order->getCustomSerialNumber()
        ];

        return $this->sendAPI(LotteryTransferPointRequestDTO::class, $sendData, 1090003);
    }

    /**
     * 查詢餘額(點數、信用共用)
     * @return mixed
     */
    public function accountPoint()
    {
        $sendData = [
            'account' => $this->account
        ];
        if ($this->bchType == 1) {
            return $this->sendAPI(LotteryUserInfoRequestDTO::class, $sendData, 1090001);
        } elseif ($this->bchType == 2) {
            $res = $this->sendAPI(LotteryCreditLimitInfoRequestDTO::class, $sendData, 1090001);
            if (count($res['data']) > 0) {
                $res['data'] = [
                    'account'     => $res['data'][0]['account'],
                    'creditLimit' => $res['data'][0]['creditLimit'],
                    'credit'      => $res['data'][0]['currentCredit']
                ];
            }

            return $res;
        } else {
            return $this->logAndReturn(__FUNCTION__ . ' ; account: ' . $this->account . ' ; error bchType: ' . $this->bchType, 1090001);
        }
    }

    /**
     * 直接查詢帳號、不存在則直接建立
     * @return mixed
     */
    public function checkCreateAccount()
    {
        $res = $this->accountInfo();
        if ($res['code'] == 891203) {
            return $this->createAccount();
        }

        return $res;
    }

    /**
     * 建立帳號
     * @return mixed
     */
    public function createAccount()
    {
        $sendData = [
            'account'   => $this->account,
            'nickname'  => $this->account,
            'limitWin'  => $this->getLimitWin(),
            'limitLose' => $this->getLimitLose(),
            'groupId'   => (($this->bchType == 1) ? 0 : 1)
        ];

        return $this->sendAPI(LotteryPlayerRegisterRequestDTO::class, $sendData, 1090004);
    }

    /**
     * 查詢帳號
     * @return mixed
     */
    public function accountInfo()
    {
        $sendData = [
            'account' => $this->account
        ];

        return $this->sendAPI(LotteryUserInfoRequestDTO::class, $sendData, 1090005);
    }

    /**
     * 設定帳號模式(啟用/停用)
     * @param int $status
     * @return mixed
     */
    public function assignAccountsMode(int $status)
    {
        $change = [
            '3'  => 0,
            '-2' => 2
        ];
        $sendData = [
            'account' => $this->account,
            'mode'    => $change[$status]
        ];

        return $this->sendAPI(LotterySetUserModeRequestDTO::class, $sendData, 1090006);
    }

    /**
     * 設定信用額度
     * @param int $quota
     * @return mixed
     */
    public function assignQuota(int $quota)
    {
        $sendData = [
            'account'     => $this->account,
            'creditLimit' => $quota
        ];

        return $this->sendAPI(LotterySetUserCreditRequestDTO::class, $sendData, 1090007);
    }

    /**
     * 取得登入遊戲網址
     * @return mixed
     */
    public function buildGameURL()
    {
        $sendData = [
            'account' => $this->account,
        ];

        return $this->sendAPI(LotteryGameUrlRequestDTO::class, $sendData, 1090008);
    }

    /**
     * 設定佔成
     * @param $percent
     * @return mixed
     */
    public function assignPercent($percent)
    {
        $sendData = [
            'account'     => $this->account,
            'percent'     => $percent,
            'lotteryType' => $this->getLotteryType()
        ];

        return $this->sendAPI(LotteryUserHoldingRequestDTO::class, $sendData, 1090009);
    }

    /**
     * 設定退水
     * @param $commission
     * @return mixed
     */
    public function assignCommission($commission)
    {
        $sendData = [
            'account'     => $this->account,
            'lotteryType' => $this->getLotteryType(),
            'refund'      => $commission
        ];

        return $this->sendAPI(LotterySetUserRefundRequestDTO::class, JsonUtility::toJson(['data' => [$sendData]]), 1090010);
    }

    /**
     * 設定暱稱
     * @param string $nickname
     * @return mixed
     */
    public function assignNickName(string $nickname)
    {
        $sendData = [
            'account'  => $this->account,
            'nickname' => $nickname
        ];

        return $this->sendAPI(LotteryUserModifyNickNameRequestDTO::class, $sendData, 1090011);
    }

    /**
     * 設定多人帳號模式(停用/啟用)
     * @param array $accounts
     * @param int $status
     * @return mixed
     */
    public function multipleAssignAccountsMode(array $accounts, int $status)
    {
        $sendData = [
            'account' => ArrayUtility::implode($accounts),
            'mode'    => $status
        ];

        return $this->sendAPI(LotterySetUserModeRequestDTO::class, $sendData, 1090006);
    }

    /**
     * 設定信用額度回復模式
     */
    public function assignCreditRecoverGroup($groupId)
    {
        // groupId參數名稱可能會改變
        $sendData = [
            'account' => $this->account,
            'groupId' => $groupId
        ];

        return $this->sendAPI(LotterySetCreditResetGroupRequestDTO::class, $sendData, 1090012);
    }

    /**
     * 設定現金會員範本
     */
    public function assignCashTemplate($exampleType)
    {
        $sendData = [
            'account'     => $this->account,
            'exampleType' => $exampleType
        ];

        return $this->sendAPI(LotterySetUserStakeLimitRequestDTO::class, $sendData, 1090013);
    }

    /**
     * 查詢範本
     */
    public function stakeTemplate(string $type = 'All')
    {
        $sendData = [
            'type' => $type
        ];

        return $this->sendAPI(LotteryStakeLimitRequestDTO::class, $sendData, 1090014);
    }

    /**
     * 輸贏回復
     */
    public function recoverWinLose()
    {
        $sendData = [
            'account' => $this->account
        ];

        return $this->sendAPI(LotteryResetUserLimitRequestDTO::class, $sendData, 1090015);
    }

    /**
     * 設定帳號限贏限輸
     * @param int $winLimit
     * @param int $loseLimit
     */
    public function assignWinLoseLimit(int $winLimit, int $loseLimit)
    {
        $sendData = [
            'account' => $this->account,
            'limit'   => $winLimit
        ];
        $winRes = $this->sendAPI(LotteryUserWinLimitRequestDTO::class, $sendData, 1090016);
        if ($winRes['code'] != 0) {
            return $winRes;
        }
        $sendData ['limit'] = $loseLimit;

        return $this->sendAPI(LotteryUserLoseLimitRequestDTO::class, $sendData, 1090017);
    }

    /**
     * 檢查玩家帳號是否存在(取得玩家目前總輸贏流程用)
     * @param string $account
     * @return mixed
     */
    public function checkRemoteAccount(string $account)
    {
        $sendData = [
            'account' => $account
        ];
        $res = $this->sendAPI(LotteryUserInfoRequestDTO::class, $sendData, 1090018);
        if ($res['code']) {
            if ($res['code'] == 891203) {
                return $this->returnMsg(0);
            }
        }

        return $res;
    }

    /**
     * 玩家限注查詢
     * @param string $account
     * @return mixed
     */
    public function accountTotalWinLose(string $account)
    {
        $sendData = [
            'account' => $account
        ];
        $res = $this->sendAPI(LotteryUserLimitInfoRequestDTO::class, $sendData, 1090019);
        if ($res['code'] == 0 && count($res['data']) == 1) {
            return $this->returnMsg(0, $res['data'][0]['winCredit']);
        }

        return $res;
    }

    /**
     * 踢出玩家
     * @return mixed
     */
    public function logout()
    {
        $sendData = [
            'account' => $this->account
        ];

        return $this->sendAPI(LotteryKickUserRequestDTO::class, $sendData, 1090020);
    }

    /**
     * 設定所有返水
     * @param array $accountList 帳號列表(如:['john','Tom'])
     * @param array $commissionSetting 玩法返水設定(如[10003 => 10, 10004 => 20])
     * @return mixed
     */
    public function assignAllCommission(array $accountList, array $commissionSetting)
    {
        $sendData = [];
        if (ArrayUtility::isList($accountList) && ArrayUtility::isList($commissionSetting)) {
            foreach ($commissionSetting as $lotteryType => $commission) {
                $sendData[] = [
                    'account'     => ArrayUtility::implode($accountList),
                    'lotteryType' => $lotteryType,
                    'refund'      => $commission
                ];
            }
        }

        return $this->sendAPI(LotterySetUserRefundRequestDTO::class, JsonUtility::toJson(['data' => $sendData]), 1090021);
    }

    /**
     * 設定所有佔成
     * @return mixed
     */
    public function assignAllPercent()
    {
        // TODO: Implement assignAllPercent() method.
    }

    /**
     * 一次設定多人信用額度
     * @param string $playerList 用逗號串起來的帳號字串
     * @param int $creditPoint
     */
    public function multipleAssignQuota(string $playerList, int $creditPoint)
    {
        $sendData = [
            'account'     => $playerList,
            'creditLimit' => $creditPoint
        ];

        return $this->sendAPI(LotterySetUserCreditRequestDTO::class, $sendData);
    }

    /**
     * 踢多玩家下線
     * @param string $playerList
     * @return mixed
     */
    public function multipleKickOutPlayer(string $playerList)
    {
        $sendData = [
            'account' => $playerList
        ];

        return $this->sendAPI(LotteryKickUserRequestDTO::class, $sendData);
    }
}
