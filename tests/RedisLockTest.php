<?php

require_once '../vendor/autoload.php';

use Ar414\RedisLock;

$redis = new \Redis();
$redis->connect('127.0.0.1','6379');
$lockTimeOut = 5;
$redisLock = new RedisLock($redis,$lockTimeOut);

$lockKey = 'lock:user:wallet:uid:1001';
$lockExpire = $redisLock->getLock($lockKey);
var_dump($lockExpire);
if($lockExpire) {
    try {
        //select user wallet balance for uid
        $userBalance = 100;
        //select goods price for goods_id
        $goodsPrice = 80;

        if($userBalance >= $goodsPrice){
            $newUserBalance = $userBalance - $goodsPrice;
            var_dump($newUserBalance);
            //TODO set user balance in db
        }else{
            throw new Exception('user balance insufficient');
        }
        $redisLock->releaseLock($lockKey,$lockExpire);
    }
    catch (\Throwable $throwable) {
        $redisLock->releaseLock($lockKey,$lockExpire);
        throw new Exception('Busy network');
    }
}