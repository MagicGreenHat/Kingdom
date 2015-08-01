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
        $hash = $request->getSession()->getId();

        /** @var RedisClientInterface $redis */
        $redis = $this->container->get('snc_redis.default');

        $userHash = $redis->hgetall(RedisClientInterface::CHARACTERS_HASH_NAME);

        $username = $this->getUser()->getUsername();

        if ($oldHash = array_search($username, $userHash)) {
            $redis->hdel(RedisClientInterface::CHARACTERS_HASH_NAME, $oldHash);
        }

        $redis->hset(RedisClientInterface::CHARACTERS_HASH_NAME, $hash, $username);

        return $this->render('RottenwoodKingdomBundle:Default:game.html.twig',
            [
                'hash'               => $hash,
                'onlinePlayersCount' => $redis->hlen(RedisClientInterface::CHARACTERS_HASH_NAME),
            ]
        );
    }
}
