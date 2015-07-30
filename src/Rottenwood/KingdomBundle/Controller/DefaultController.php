<?php

namespace Rottenwood\KingdomBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller {

    /**
     * //TODO: Сделать главную страницу
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
     * @Route("/game", name="game_page")
     * @return Response
     */
    public function gamePageAction(Request $request) {
        return $this->render('RottenwoodKingdomBundle:Default:game.html.twig',
            ['hash' => $request->getSession()->getId()]
        );
    }
}
