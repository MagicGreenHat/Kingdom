<?php
/**
 * Author: Rottenwood
 * Date Created: 31.07.15 19:40
 */
namespace Rottenwood\KingdomBundle\Redis;

interface RedisClientInterface {

    const CHARACTERS_HASH_TEMPORARY = 'kingdom:characters:hash:temp';
    const CHARACTERS_HASH_NAME = 'kingdom:characters:hash';
    const ID_USERNAME_HASH = 'kingdom:usernames';
    const ID_SESSION_HASH = 'kingdom:sessions';

    public function get($key);

    public function hset($hash, $key, $value);

    public function hget($hash, $key);

    public function hmget($hash, $key);

    public function hgetall($hash);

    public function hlen($hash);

    public function hdel($hash, $key);

    public function del($key);
}
