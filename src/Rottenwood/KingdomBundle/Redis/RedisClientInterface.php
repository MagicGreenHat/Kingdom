<?php
/**
 * Author: Rottenwood
 * Date Created: 31.07.15 19:40
 */
namespace Rottenwood\KingdomBundle\Redis;

interface RedisClientInterface {

    const ID_USERNAME_HASH = 'kingdom:users:usernames';
    const ID_SESSION_HASH = 'kingdom:users:sessions';
    const ID_ROOM_HASH = 'kingdom:users:rooms';
    const SESSION_ID_HASH = 'kingdom:sessions:users';
    const ONLINE_LIST = 'kingdom:users:online';

}
