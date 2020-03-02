<?php

namespace app\index\controller;


use app\index\service\BtcService;
use app\common\util\BlockChain;
use think\Db;
use think\Exception;
use think\Request;

/**
 * BTC
 * Class Btc
 * @package app\index\controller
 */
class Btc
{

    public function __construct(Request $request = null)
    {
        set_time_limit(0);
    }

    /**
     * 获取链上区块高度
     * @return array
     */
    public function height()
    {
        $result = [];
        try {
            $symbol = 'btc';
            $blockService = new BtcService();
            $data = $blockService->getBlockHeightBtc_API_httpGet();
            $data = json_decode($data, true);
            if (!empty($data) && is_array($data) && isset($data['height']) && $data['height'] != '' && $data['hash'] != '') {

                $height = $data['height'];
                $btc_block = Db::name('latest_block')->where(['symbol' => $symbol, 'height' => $height])->order('id desc')->find();
                if (!empty($btc_block)) {
                    return $this->successResult('success latest block');
                }
                //获取到最新区块高度ltc
                if (isset($data['height']) && !empty($data['height'])) {
                    $insert = [
                        'symbol' => $symbol,
                        'height' => $height,
                        'block_index' => $data['block_index'],
                        'hash' => $data['hash'],
                        'add_time' => time(),
                    ];
                }
                $result = Db::name('latest_block')->insert($insert);
                if ($result)
                    return $this->successResult('success');
                return $this->failResult('insert error', 1);
            } else {
                return $this->failResult('select data error', 1);
            }
        } catch (Exception $e) {
            $result = $this->failResult($e->getMessage(), 1);
        }
        return $result;
    }

    /**
     * BTC recharge
     * @return array|bool
     */
    public function sure()
    {
        $result = [];
        try {
            $symbol = 'btc';
            $local = Db::name('local_block')->where(['symbol' => $symbol])->order('id desc')->find();
            $lastest = Db::name('latest_block')->where(['symbol' => $symbol])->order('id desc')->find();
            $local_height = $local['height'];
            $last_height = $lastest['height'];
            $btcService = new BtcService();
            $member_address = $btcService->getMemberAddress('btc');
            if ($last_height - $local_height >= 2) {
                $result = false;

                for ($i = $local_height + 1; $i < $last_height; $i++) {

                    $data = $btcService->getBlockDetailBtc_API_httpGet($i);
                    $data = json_decode($data, true);
                    if (!empty($data) && is_array($data) && isset($data['blocks']) && !empty($data['blocks'][0]['hash']) != '') {
                        //开启事务处理
                        Db::startTrans();
                        $blocks = $data['blocks'];

                        $result = $btcService->dealBlockDetailBtcBlock($data, $member_address);
                        if (!$result) {
                            Db::rollback();
                            return $this->failResult('btc block deal error', 1);
                        }
                        $btc_height = Db::name('local_block')->where(['symbol'=>'btc','height'=>$i])->order('id desc')->find();
                        if(empty($btc_height)){
                            $insert_lock_data = [
                                'symbol' => $symbol,
                                'hash' => $blocks[0]['hash'],
                                'height' => $i,
                                'block_index' => $blocks[0]['block_index'],
                                'add_time' => time(),
                            ];
                            $result = Db::name('local_block')->insert($insert_lock_data);
                            if (!$result) {
                                Db::rollback();
                                return $this->failResult('insert local_block record error', 1);
                            }
                        }
                        Db::commit();
                    }

                }
            }
            return $this->successResult($result);
        } catch (Exception $e) {
            $result = $this->failResult($e->getMessage(), 1);
        }
        return $result;
    }


    protected function successResult($data = [])
    {
        return $this->getResult(0, 'success', $data);
    }

    protected function failResult($message, $status = 1)
    {
        return $this->getResult($status, $message, false);
    }

    protected function getResult($status, $message, $data)
    {

        return [
            'status' => $status,
            'msg' => $message,
            'data' => $data
        ];
    }


}
