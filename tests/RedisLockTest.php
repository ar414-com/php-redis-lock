<?php

require_once '../vendor/autoload.php';

use Ar414\RedisLock;

$redis = new \Redis();
$redis->connect('127.0.0.1','6379');
$lockTimeOut = 5;
$redisLock = new RedisLock($redis,$lockTimeOut);

$lockKey = 'lock:user:wallet:uid:1001';
$isGet = $redisLock->getLock($lockKey);
var_dump($isGet);
if($isGet) {
    try {
        //select user wallet balance for uid
        $userBalance = 100;
        //select goods price for goods_id
        $goodsPrice = 80;

        if($userBalance >= $goodsPrice){
            $newUserBalance = $userBalance - $goodsPrice;
            //TODO set user balance in db
        }else{
            throw new Exception('user balance insufficient');
        }
        $redisLock->releaseLock($lockKey,$isGet);
    }
    catch (\Throwable $throwable) {
        $redisLock->releaseLock($lockKey,$isGet);
        throw new Exception('Busy network');
    }
}