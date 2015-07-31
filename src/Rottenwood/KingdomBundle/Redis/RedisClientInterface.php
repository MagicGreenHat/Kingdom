<?php
/**
 * Author: Rottenwood
 * Date Created: 31.07.15 19:40
 */
namespace Rottenwood\KingdomBundle\Redis;

interface RedisClientInterface {

    public function get($key);

    public function hset($hash, $key, $value);

    public function hgetall($hash);

    public function hlen($hash);
}
