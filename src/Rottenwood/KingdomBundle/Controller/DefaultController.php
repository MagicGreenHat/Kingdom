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

        /** @var RedisClientInterface $redis */
        $redis = $this->container->get('snc_redis.default');

        $charactersOnline = $redis->hgetall(RedisClientInterface::CHARACTERS_HASH_NAME);

        $user = $this->getUser();
        $userId = $user->getId();
        $username = $user->getUsername();

        $userData = [
            'id'   => $userId,
            'name' => $username,
        ];

        if ($oldCharacterData = array_search(json_encode($userData), $charactersOnline)) {
            $redis->hdel(RedisClientInterface::CHARACTERS_HASH_NAME, $oldCharacterData);
        }

        $redis->hset(RedisClientInterface::CHARACTERS_HASH_NAME,
            $sessionId,
            json_encode($userData)
        );

        return $this->render('RottenwoodKingdomBundle:Default:game.html.twig',
            [
                'sessionId'          => $sessionId,
                'onlinePlayersCount' => $redis->hlen(RedisClientInterface::CHARACTERS_HASH_NAME),
            ]
        );
    }
}
