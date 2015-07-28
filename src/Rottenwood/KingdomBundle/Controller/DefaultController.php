<?php

namespace Rottenwood\KingdomBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use WebSocket\Client as WebsocketClient;

class DefaultController extends Controller {

    /**
     * @Route("/", name="index")
     * @return Response
     */
    public function indexAction() {
        return $this->render('RottenwoodKingdomBundle:Default:index.html.twig');
    }
}
