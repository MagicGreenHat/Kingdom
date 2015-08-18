<?php
/**
 * Author: Rottenwood
 * Date Created: 31.07.15 19:40
 */
namespace Rottenwood\KingdomBundle\Redis;

interface RedisClientInterface {

    const ID_USERNAME_HASH = 'kingdom:users:usernames';
    const ID_SESSION_HASH = 'kingdom:users:sessions';
    const SESSION_ID_HASH = 'kingdom:sessions:users';
    const ONLINE_LIST = 'kingdom:users:online';

    public function get($key);

    public function hset($hash, $key, $value);

    public function hget($hash, $key);

    public function hmget($hash, $key);

    public function hgetall($hash);

    public function hlen($hash);

    public function hdel($hash, $key);

    public function del($key);

    public function sadd($key, $value);

    public function smembers($key);
}
