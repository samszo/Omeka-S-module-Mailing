<?php
namespace Mailing;

use Omeka\Module\AbstractModule;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\Mvc\MvcEvent;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function install(ServiceLocatorInterface $serviceLocator)
    {
        $settings = $serviceLocator->get('Omeka\Settings');
        $settings->set('mailing_listmonk_url', '');
        $settings->set('mailing_listmonk_username', '');
        $settings->set('mailing_listmonk_password', '');
        $settings->set('mailing_default_list_id', '');
    }

    public function uninstall(ServiceLocatorInterface $serviceLocator)
    {
        $settings = $serviceLocator->get('Omeka\Settings');
        $settings->delete('mailing_listmonk_url');
        $settings->delete('mailing_listmonk_username');
        $settings->delete('mailing_listmonk_password');
        $settings->delete('mailing_default_list_id');
    }

    public function getConfigForm(PhpRenderer $renderer)
    {
        $settings = $this->getServiceLocator()->get('Omeka\Settings');
        
        $formElements = [
            [
                'name' => 'mailing_listmonk_url',
                'type' => 'Text',
                'options' => [
                    'label' => 'Listmonk URL',
                    'info' => 'The base URL of your Listmonk installation (e.g., https://listmonk.example.com)',
                ],
                'attributes' => [
                    'value' => $settings->get('mailing_listmonk_url', ''),
                    'required' => true,
                ],
            ],
            [
                'name' => 'mailing_listmonk_username',
                'type' => 'Text',
                'options' => [
                    'label' => 'Listmonk Username',
                    'info' => 'Username for Listmonk API authentication',
                ],
                'attributes' => [
                    'value' => $settings->get('mailing_listmonk_username', ''),
                    'required' => true,
                ],
            ],
            [
                'name' => 'mailing_listmonk_password',
                'type' => 'Password',
                'options' => [
                    'label' => 'Listmonk Password',
                    'info' => 'Password for Listmonk API authentication',
                ],
                'attributes' => [
                    'value' => $settings->get('mailing_listmonk_password', ''),
                    'required' => true,
                ],
            ],
            [
                'name' => 'mailing_default_list_id',
                'type' => 'Text',
                'options' => [
                    'label' => 'Default List ID',
                    'info' => 'Default mailing list ID in Listmonk',
                ],
                'attributes' => [
                    'value' => $settings->get('mailing_default_list_id', ''),
                ],
            ],
        ];

        $html = '';
        foreach ($formElements as $elementInfo) {
            $html .= $renderer->formRow($renderer->formElement($elementInfo));
        }
        
        return $html;
    }

    public function handleConfigForm(AbstractController $controller)
    {
        $settings = $this->getServiceLocator()->get('Omeka\Settings');
        $params = $controller->params()->fromPost();

        $settings->set('mailing_listmonk_url', $params['mailing_listmonk_url']);
        $settings->set('mailing_listmonk_username', $params['mailing_listmonk_username']);
        $settings->set('mailing_listmonk_password', $params['mailing_listmonk_password']);
        $settings->set('mailing_default_list_id', $params['mailing_default_list_id']);

        return true;
    }
}
