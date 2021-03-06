PhpRedisBundle
==============

Symfony 2 Bundle for phpredis extension

---

!!!WARNING
==========
This bundle is under construction. It is highly recommended not using this bundle for production

---

WORKING METHODS
===============
* hashes: hDel, hExists, hGet, hGetAll, hIncrBy, hIncrByFloat, hKeys, hLen, hMGet, hMSet, hSet
* keys: del, dump, exists, expire, expireAt, keys, move, object, persist, randomKey, rename, renameNx, type, sort, ttl, restore
* lists: brPop, blPop, brPoplPush, lGet, lGetRange, lInsert, lLen, lIndex, lPush, lPushx, lRange, lRem, lRemove, lSet, lSize, lTrim, ListTrim, rPop, rPopLPush, rPush, rPushx
* sets: sAdd, sCard, sContains, sDiff, sDiffStore, sGetMembers, sInter, sInterStore, sIsMember, sMembers, sMove, sPop, sRandMember, sRem, sRemove, sSize, sUnion, sUnionStore
* sorted sets: zAdd, zCard, zCount, zIncrBy, zInter, zRange, zRangeByScore, zRevRangeByScore, zRank, zRevRank, zSize
* strings: append, bitCount, decr, get, getBit, getRange, getSet, incr, incrByFloat, mget, mset, set, setBit, setex, setnx, setRange, strlen
* server: bgrewriteaof, bgsave, config, dbSize, flushAll, flushDB, info, lastSave, resetStat, save, slaveOf, time, slowlog
* connection: auth, close, echo (cEcho), getOption, ping, select, setOption

---

Missing Integration Tests
=========================
* keys: sort (waiting for sadd function)
* lists: blPop, brPop,
* server: resetStats, slaveOf

Methods Ignored
===============
* connection: connect, pconnect, open, popen
* strings: delete, incrBy, decrBy, getMultiple, mSetNx, psetex
* keys: getKeys, setTimeout, pexpire, pexpireAt, renameKey, pttl

Methods not working
===================
* strings: bitOp
* keys: migrate


Method Informations
===================
* set: parameter timeout is defined as float in docbloc and function. It is an int or long. Float will rase an error
* bitOf: always returns 0
* migrate: returns false when successful
* lTrim: listTrim: return value is commented as array. Bool will be returned
* lPush: if optional parameter will be null, it inserts plank strings
* rPush: if optional parameter will be null, it inserts plank strings
* sDiff: Third param seems to be ignored
* sDiffStore: Third param seems to be ignored
* sInter: Third param seems to be ignored
* sInterStore: Third param seems to be ignored
* zInter: outKey is not stored, weight is not working

---

Configuration
=============
here is a first sample configuration

be aware of the logging flag. When you run many redis commands and logging is enabled on production system,
you could get a memory limit exceeded error. The logger redis client is collecting every command in a data collector.

**config.yml**
```
php_redis:
    clients:
        default:
            host: localhost
            port: ~
            db: 0
            pconnect: true
            logging: true
            connection_timeout: 1
        importstatus:
            host: localhost
            port: ~
            db: 1
            pconnect: true
            logging: true
```

---

Testing within Symfony2
=======================

for running the unit and integration tests add test parameters to your config:
**config_test.yml**
```
parameters:
  redis:
    host: localhost
    port: 6379
    db: 10
    db2: 9
```

Please keep in mind, that you have to run your own redis server.

**Information** The flushAll command will be performed in tests.
It would be better to run an own instance for the tests

---