<?php

namespace Rottenwood\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class RottenwoodUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
