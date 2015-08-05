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
        $user = $this->getUser();

        return $this->render('RottenwoodKingdomBundle:Default:game.html.twig',
            [
                'sessionId' => $request->getSession()->getId(),
                'userId'    => $user->getId(),
                'username'  => $user->getUsername(),
            ]);
    }
}
