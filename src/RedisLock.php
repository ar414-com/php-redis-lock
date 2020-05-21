<?php

namespace Ar414;


class RedisLock
{
    private $redis;
    private $timeout = 3;

    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public function getLockCacheKey($key)
    {
        return "lock_{$key}";
    }

    public function getLock($key, $timeout = NULL)
    {
        $timeout = $timeout ? $timeout : $this->timeout;
        $lockCacheKey = $this->getLockCacheKey($key);
        $expireAt = time() + $timeout;
        
        $isGet = (bool)$this->redis->setnx($lockCacheKey, $expireAt);
        if ($isGet) {
            return $expireAt;
        }
    
        while (1) {
            usleep(10);
            $time = time();
            $oldExpire = $this->redis->get($lockCacheKey);
            if ($oldExpire >= $time) {
                continue;
                 
            }

            $newExpire = $time + $timeout;
            $expireAt = $this->redis->getset($lockCacheKey, $newExpire);
            return $expireAt;
           
        }
    }

    public function releaseLock($key, $newExpire)
    {
        $lockCacheKey = $this->getLockCacheKey($key);
        if ($newExpire >= time()) {
            return $this->redis->del($lockCacheKey);
        }
        return true;
    }

}
