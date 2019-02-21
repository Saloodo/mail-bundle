<?php

namespace Saloodo\MailBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('saloodo_mail');

        $rootNode
            ->children()
                ->scalarNode('cache_driver')->end()
                ->scalarNode('adapter')->end()
                ->arrayNode('salesforce')->children()
                    ->scalarNode('id')->end()
                    ->scalarNode('secret')->end()
                    ->scalarNode('tenant_subdomain')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
