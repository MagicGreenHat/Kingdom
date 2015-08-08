<?php

namespace Rottenwood\KingdomBundle\Service;

use Rottenwood\KingdomBundle\Entity\Room;
use Rottenwood\KingdomBundle\Entity\User;
use Rottenwood\KingdomBundle\Entity\Infrastructure\UserRepository;
use Rottenwood\KingdomBundle\Redis\RedisClientInterface;
use Snc\RedisBundle\Client\Phpredis\Client;

class UserService {

    /** @var RedisClientInterface */
    private $redis;
    /** @var UserRepository */
    private $userRepository;

    /**
     * @param Client         $redis
     * @param UserRepository $userRepository
     */
    public function __construct(Client $redis, UserRepository $userRepository) {
        $this->redis = $redis;
        $this->userRepository = $userRepository;
    }

    /**
     * Запрос всех онлайн игроков в комнате
     * @param Room  $room
     * @param array $excludePlayerIds
     * @return User[]
     */
    public function getOnlineUsersInRoom(Room $room, $excludePlayerIds = []) {
        if (!is_array($excludePlayerIds)) {
            $excludePlayerIds = [$excludePlayerIds];
        }

        return $this->userRepository->findOnlineByRoom($room, $this->getOnlineUsersIds(), $excludePlayerIds);
    }

    /**
     * Запрос ID всех онлайн игроков в комнате
     * @param Room  $room
     * @param array $excludePlayerIds
     * @return int[]
     */
    public function getOnlineUsersIdsInRoom(Room $room, $excludePlayerIds = []) {
        return array_map(
            function (User $user) {
                return $user->getId();
            },
            $this->getOnlineUsersInRoom($room, $excludePlayerIds)
        );
    }

    /**
     * Запрос id всех игроков онлайн из redis
     * @return int[]
     */
    public function getOnlineUsersIds() {
        return array_map(
            function ($player) {
                return json_decode($player, true)['id'];
            },
            $this->redis->hgetall(RedisClientInterface::CHARACTERS_HASH_NAME)
        );
    }

    /**
     * @param array $userIds
     * @return array
     */
    public function getSessionsByUserIds(array $userIds) {
        return array_values($this->redis->hmget(RedisClientInterface::ID_SESSION_HASH, $userIds));
    }
}
