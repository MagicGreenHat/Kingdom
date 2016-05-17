<?php

namespace Rottenwood\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/** {@inheritdoc} */
class RottenwoodUserBundle extends Bundle
{

    /** {@inheritdoc} */
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
