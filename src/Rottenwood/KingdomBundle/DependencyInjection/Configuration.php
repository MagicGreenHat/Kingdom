<?php

namespace Rottenwood\KingdomBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/** {@inheritDoc} */
class Configuration implements ConfigurationInterface
{

    /** {@inheritDoc} */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('rottenwood_kingdom');

        return $treeBuilder;
    }
}
