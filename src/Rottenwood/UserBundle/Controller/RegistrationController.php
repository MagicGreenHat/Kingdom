<?php

namespace Rottenwood\UserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as FOSURegistrationController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegistrationController extends FOSURegistrationController
{

    /**
     * Переназначение родительского метода, отвечающего за страницу уведомления о регистрации
     * Данный метод перенаправляет пользователя сразу на основную страницу игры
     * @return RedirectResponse
     */
    public function confirmedAction()
    {
        return $this->redirectToRoute('game_page');
    }
}
