<?php

namespace app\index\service;


use app\common\util\BlockChain;
use think\Db;

/**
 * Btc Service
 * Class BaseService*/
class  BtcService extends BaseService
{

    public function getBlockHeightBtc_API_httpGet(){
        $url = BlockChain::BLOCK_CHAIN_HEIGHT_BTC_URL;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $output = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            return false;
        }
        return $output;
    }

    /**
     * blockchain data
     * @param $height
     * @return bool|string
     */
    public function getBlockDetailBtc_API_httpGet($height)
    {
        $url = BlockChain::BLOCK_CHAIN_DETAIL_BTC_URL.$height . '?format=json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 关闭SSL验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $output = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            return false;
        }
        return $output;
    }


    private function doChecker($tidy_detail,$member_address)
    {
        if ($tidy_detail) {
            foreach ($tidy_detail as $key => $item) {
                //外层是交易对象
                $hash = $item['hash'];
                $out = $item['out'];
                if (!empty($out)) {
                    foreach ($out as $key2 => $out_single_item) {
                        $addr = strtolower($out_single_item['addr']);
                        $value = $out_single_item['value'];
                        //check btc address
                        if(in_array($addr,$member_address['address'])){

                            $record = Db::name('recharge_record')->where(['recharge_address'=>$out_single_item['addr'],'symbol'=>'btc','txid'=>$hash])->find();
                            if ($record) {
                                continue;
                            }

                            $insert_recharge_data = [
                                'member_id' => $member_address['data'][$addr]['member_id'],
                                'recharge_address' => $out_single_item['addr'],
                                'symbol' => 'btc',
                                'txid' => $hash,
                                'amount' => $value,
                                'status'=>1,
                                'create_time' => time()
                            ];
                            $res = Db::name('recharge_record')->insert($insert_recharge_data);
                            if (!$res)
                                return 0;

                        }

                    }
                }
            }
        }
        return 1;
    }


    private function tidyBlockDetail($block_detail)
    {
        $ret_data = array();
        if ($block_detail && isset($block_detail['blocks'])) {
            $blocks = $block_detail['blocks'];
            if (!empty($blocks)) {
                for ($i = 0; $i < count($blocks); $i++) {
                    $current_obj = $blocks[$i];
                    if (!empty($current_obj['tx'])) {
                        foreach ($current_obj['tx'] as $key0 => $tx_item) {
                            if (!empty($tx_item['out'])) {
                                $out_item_arr = array();
                                foreach ($tx_item['out'] as $key => $item) {
                                    if (isset($item['addr'])
                                        && isset($item['value'])
                                        && isset($item['n'])
                                    ) {
                                        $tmpItem = [
                                            'n' => $item['n'],
                                            'addr' => $item['addr'],
                                            'value' => $item['value'] * pow(10, -8),
                                        ];
                                        array_push($out_item_arr, $tmpItem);
                                    }
                                }
                                $tmp_tx_item = [
                                    "tx_index" => $tx_item['tx_index'],
                                    "hash" => $tx_item['hash'],
                                    "out" => $out_item_arr
                                ];
                                array_push($ret_data, $tmp_tx_item);
                            }
                        }
                    }
                }
            }
        }
        return $ret_data;
    }

    public function dealBlockDetailBtcBlock($block_detail, $member_address)
    {
        //整理成规整的结构
        $tidy_detail = $this->tidyBlockDetail($block_detail);
        if ($tidy_detail) {
            //查询当前块的详情
            $this->doChecker($tidy_detail,$member_address);

        }
        return 1;
    }

    public function getMemberAddress($symbol)
    {
        $arr = [
            'address' => [],
            'data' => [],
        ];
        $data = Db::name('member_recharge_address')->field('member_id,symbol,member_address')->where(['symbol' => $symbol])->order('id desc')->select();
        if (empty($data)) {
            return $arr;
        }

        foreach ($data as $k => $v) {
            $v['member_address'] = strtolower($v['member_address']);
            $arr['data'][$v['member_address']] = $v;
            $arr['address'][$k] = $v['member_address'];
        }
        return $arr;
    }



}