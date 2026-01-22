<?php declare(strict_types=1);

namespace Mailing\Form;

use Common\Form\Element as CommonElement;
use Laminas\Form\Element;
use Laminas\Form\Form;
use Omeka\Form\Element as OmekaElement;

class ConfigForm extends Form
{
    public function init(): void
    {
        $this
            ->add([
                'name' => 'mailing_listmonk_url',
                'type' => Element\Text::class,
                'options' => [
                    'label' => 'Listmonk URL',
                    'info' => 'The base URL of your Listmonk installation (e.g., https://listmonk.example.com)',
                ],
                'attributes' => [
                    'id' => 'mailing_listmonk_url',
                    'required' => true
                ],
            ])
            ->add([
                'name' => 'mailing_listmonk_username',
                'type' => Element\Text::class,
                'options' => [
                    'label' => 'Listmonk Username',
                    'info' => 'Username for Listmonk API authentication',
                ],
                'attributes' => [
                    'id' => 'mailing_listmonk_username',
                    'required' => true
                ],
            ])
            ->add([
                'name' => 'mailing_listmonk_token',
                'type' => Element\Text::class,
                'options' => [
                    'label' => 'Listmonk Token',
                    'info' => 'Token for Listmonk API authentication',
                ],
                'attributes' => [
                    'id' => 'mailing_listmonk_token',
                    'required' => true
                ],
            ])
        ;

    }
}
