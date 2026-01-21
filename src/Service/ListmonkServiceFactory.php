<?php
namespace Mailing\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ListmonkServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $settings = $container->get('Omeka\Settings');
        
        $apiUrl = $settings->get('mailing_listmonk_url', '');
        $username = $settings->get('mailing_listmonk_username', '');
        $password = $settings->get('mailing_listmonk_password', '');

        return new ListmonkService($apiUrl, $username, $password);
    }
}
