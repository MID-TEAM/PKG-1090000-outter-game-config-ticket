<?php

namespace Mid\OutterGameConfigTicket\Services\Pharoah;

trait TraitHelper
{
    public function requestJsonToArray($attrName)
    {
        try {
            $arrData = json_decode(request($attrName), true);
        } catch (\Exception $e) {
            throw new \BackEndException(300006, ['text' => 'JSON 解析失敗']);
        }
        request()->merge([$attrName => $arrData]);
    }

    public function getValidAttrName(array $rules, $moduleName, array $renameArr = [])
    {
        $attrNameArr = [];
        foreach ($rules as $rule => $val) {
            $attrNameArr[$rule] = \Lang::get("Mid\\" . $moduleName . "::validAttrName." . $rule);
        }

        return $attrNameArr;
    }

    /**
     * 確認 資料
     * @param $data
     * @param $code
     * @param $msg
     * @throws \BackEndException
     */
    public function checkData($data, $code, $msg)
    {
        if (empty($data)) {
            throw new \BackEndException($code, $msg);
        }
    }
}
