<?php

namespace Saloodo\MailBundle\DependencyInjection;

use Saloodo\MailBundle\Adapters\SalesForceAdapter;
use Saloodo\MailBundle\Sender;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class SaloodoMailExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $adapter = $config['adapter'];

        switch ($adapter) {
            case 'salesforce':
                $this->configureSalesForce($container, $config);
                break;

            //logger is the default
            default:
                return;
                break;
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array $config
     */
    private function configureSalesForce(ContainerBuilder $container, array $config)
    {
        $adapterDefinition = $container->getDefinition(SalesForceAdapter::class);
        $adapterDefinition->replaceArgument(0, $config['salesforce']['id']);
        $adapterDefinition->replaceArgument(1, $config['salesforce']['secret']);
        $adapterDefinition->replaceArgument(2, $config['salesforce']['tenant_subdomain']);
        $adapterDefinition->replaceArgument(3, new Reference($config['cache_driver']));

        $senderDefinition = $container->getDefinition(Sender::class);
        $senderDefinition->replaceArgument(0, new Reference(SalesForceAdapter::class));
    }
}
