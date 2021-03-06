<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 19.12.13
 * Time: 09:02
 */

namespace Dawen\Bundle\PhpRedisBundle\Tests\Integration;

use Dawen\Bundle\PhpRedisBundle\Component\RedisClient;
use Dawen\Bundle\PhpRedisBundle\Tests\Fixtures\AbstractKernelAwareTest;

class RedisClientConnectionIntegrationTest extends AbstractKernelAwareTest
{

    /** @var RedisClient */
    private $client;
    private $skipped = false;
    private $params;


    public function setUp()
    {
        parent::setUp();

        if($this->container->hasParameter('redis'))
        {
            $redisParams = $this->container->getParameter('redis');
            $this->params = $redisParams;
            if(!empty($redisParams['host']) && !empty($redisParams['port']))
            {
                $redis = new \Redis();
                $connected = $redis->pconnect($redisParams['host'], $redisParams['port']);
                if(!$connected) {
                    $this->skipped = true;
                    $this->markTestSkipped('could not connect to server');
                }
                $redis->select($redisParams['db']);

                $this->client = new RedisClient($redis);
            }
            else
            {
                $this->skipped = true;
                $this->markTestSkipped('parameter port and host must be set and filled');
            }
        }
        else
        {
            $this->skipped = true;
            $this->markTestSkipped('no parameters in config_test set');
        }
    }

    public function tearDown()
    {
        parent::tearDown();

        if(!$this->skipped)
        {
            $this->client->flushDB();
            $this->client->close();
        }

        $this->client = null;
        $this->params = null;
        $this->skipped = false;
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Dawen\Bundle\PhpRedisBundle\Component\RedisClientInterface', $this->client);
    }

    public function testSelect()
    {
        $key1 = 'key1';
        $key2 = 'key2';
        $value1 = 'value1';
        $value2 = 'value2';

        $successSet1 = $this->client->set($key1, $value1);
        $this->assertTrue($successSet1);

        $successSelect2 = $this->client->select($this->params['db2']);
        $this->assertTrue($successSelect2);

        $successSet2 = $this->client->set($key2, $value2);
        $this->assertTrue($successSet2);

        $exists2 = $this->client->exists($key2);
        $this->assertTrue($exists2);

        $successFlushDb2 = $this->client->flushDB();
        $this->assertTrue($successFlushDb2);

        $successSelect1 = $this->client->select($this->params['db']);
        $this->assertTrue($successSelect1);

        $exists = $this->client->exists($key1);
        $this->assertTrue($exists);
    }

    public function testCEcho()
    {
        $value = 'testechostring';

        $result = $this->client->cEcho($value);
        $this->assertSame($value, $result);
    }

    public function testPing()
    {
        $result = $this->client->ping();
        $this->assertSame('+PONG', $result);
    }

    public function testSetOption()
    {
        $name = \Redis::OPT_PREFIX;
        $valuePrefix = 'AllKEyPrefix_';
        $key = 'myKey';
        $value = 'testValue';

        $resultSetOption = $this->client->setOption($name, $valuePrefix);
        $this->assertTrue($resultSetOption);

        $resultSet = $this->client->set($key, $value);
        $this->assertTrue($resultSet);

        $resultKeys = $this->client->keys('*');
        $this->assertContains($valuePrefix . $key, $resultKeys);

    }

    public function testGetOption()
    {
        $name = \Redis::OPT_SERIALIZER;
        $expected = \Redis::SERIALIZER_NONE;

        $resultGetOption = $this->client->getOption($name);
        $this->assertSame($expected, $resultGetOption);
    }

    public function testAuth()
    {
        $password = 'testPass';

        $resultAuth = $this->client->auth($password);
        $this->assertFalse($resultAuth);
    }

}
