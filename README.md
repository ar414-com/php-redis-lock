# RedisLock

![](https://img.shields.io/badge/build-passing-brightgreen)
![](https://img.shields.io/badge/stable-v1.0.0-blue)
![](https://img.shields.io/badge/coverage-100%25-green)
![](https://img.shields.io/badge/license-MIT-brightgreen)


PHP use Redis Pessimistic Lock uses redis keys: setnx, get, getset, del

# Install
```
composer require ar414/redis-lock
```

# Usage
### New instance
```php
use Ar414\RedisLock;

$redis = new \Redis();
$redis->connect('127.0.0.1','6379');

$lockTimeOut = 5;
$redisLock = new RedisLock($redis,$lockTimeOut);
```

### Get Lock
```php
$lockKey = 'lock:user:wallet:uid:1001';
$lockExpire = $redisLock->getLock($lockKey);
if(!$lockExpire || $lockExpire < time()){
    throw new \Exception('Busy Lock');
}
//TODO：Business logic
```

### Release Lock
```
$redisLock->releaseLock($lockKey,$lockExpire);
```
