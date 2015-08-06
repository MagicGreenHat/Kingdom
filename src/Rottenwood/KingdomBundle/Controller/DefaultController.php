<?php

namespace Rottenwood\KingdomBundle\Controller;

use Rottenwood\KingdomBundle\Redis\RedisClientInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller {

    /**
     * //TODO[Rottenwood]: Сделать главную страницу
     * @Route("/", name="index")
     * @return Response
     */
    public function indexAction() {
        if ($this->getUser()) {
            return $this->redirectToRoute('game_page');
        } else {
            return $this->redirectToRoute('fos_user_security_login');
        }
    }

    /**
     * Основная страница игры
     * @Security("has_role('ROLE_USER')")
     * @Route("/game", name="game_page")
     * @param Request $request
     * @return Response
     */
    public function gamePageAction(Request $request) {
        $sessionId = $request->getSession()->getId();
        $user = $this->getUser();
        $userId =  $user->getId();
        $username =  $user->getUsername();

        $userData = [
            'id'   => $userId,
            'name' => $username,
        ];

        /** @var RedisClientInterface $redis */
        $redis = $this->container->get('snc_redis.default');

        $allSessions = $redis->hgetall(RedisClientInterface::CHARACTERS_HASH_TEMPORARY);

        if ($oldCharacterData = array_search(json_encode($userData), $allSessions)) {
            $redis->hdel(RedisClientInterface::CHARACTERS_HASH_TEMPORARY, $oldCharacterData);
        }

        $redis->hset(RedisClientInterface::CHARACTERS_HASH_TEMPORARY, $sessionId, json_encode($userData));

        $redis->hset(RedisClientInterface::USERNAMES_ID_HASH, $userId, $username);

        return $this->render('RottenwoodKingdomBundle:Default:game.html.twig', ['sessionId' => $sessionId]);
    }
}
