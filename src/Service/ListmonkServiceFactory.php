<?php
namespace Mailing\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ListmonkServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $settings = $container->get('Omeka\Settings');
         return new ListmonkService(
            $settings->get('mailing_listmonk_url', ''), 
            $settings->get('mailing_listmonk_username', ''), 
            $settings->get('mailing_listmonk_token', ''), 
            $settings->get('mailing_properties_mail', ''),
            $settings->get('mailing_properties_data', ''),
            $container->get('Omeka\ApiManager'),
            $container->get('Omeka\Logger'),
            $container->get('Omeka\Acl'),
        );
    }
}
