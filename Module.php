<?php
namespace Mailing;

if (!class_exists('Common\TraitModule', false)) {
    require_once dirname(__DIR__) . '/Common/TraitModule.php';
}

use Common\TraitModule;
use Omeka\Module\AbstractModule;
use Common\Stdlib\PsrMessage;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\ModuleManager\ModuleManager;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\Mvc\MvcEvent;


class Module extends AbstractModule
{

    const NAMESPACE = __NAMESPACE__;
    use TraitModule;

    protected $dependencies = [
        'Common',
    ];

    public function init(ModuleManager $moduleManager): void
    {
        require_once __DIR__ . '/vendor/autoload.php';
    }

    public function onBootstrap(MvcEvent $event): void
    {
        parent::onBootstrap($event);
    }

    protected function preInstall(): void
    {
        /** @var \Laminas\Mvc\I18n\Translator $translator */
        $services = $this->getServiceLocator();
        $translator = $services->get('MvcTranslator');
        $plugins = $services->get('ControllerPluginManager');
        $messenger = $plugins->get('messenger');

        if (!method_exists($this, 'checkModuleActiveVersion') || !$this->checkModuleActiveVersion('Mailing', '1.0.0.1')) {
            $message = new \Omeka\Stdlib\Message(
                $translator->translate('The module %1$s should be upgraded to version %2$s or later.'), // @translate
                'Mailing', '1.0.0.1'
            );
            throw new \Omeka\Module\Exception\ModuleCannotInstallException((string) $message);
        }

        $config = $services->get('Config');
        $basePath = $config['file_store']['local']['base_path'] ?: (OMEKA_PATH . '/files');
        if (!$this->checkDestinationDir($basePath . '/backup/log')) {
            $message = new PsrMessage(
                'The directory "{directory}" is not writeable, so old logs cannot be archived.', // @translate
                ['directory' => $basePath . '/backup/log']
            );
            $messenger->addWarning($message);
        }
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager): void
    {

        $sharedEventManager->attach(
            \Omeka\Form\SettingForm::class,
            'form.add_elements',
            [$this, 'handleMainSettings']
        );
    }

    public function getConfigForm(PhpRenderer $renderer)
    {
        $services = $this->getServiceLocator();
        $settings = $services->get('Omeka\Settings');

        return $this->getConfigFormAuto($renderer);
    }


    /*
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
            $e = new Element\Text($elementInfo);
            $html .= $renderer->formRow($renderer->formElement($e));
        }
        
        return $html;
    }
    */

    /*
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
    */
}
