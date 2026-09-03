<?php

namespace JakubFilip\Tpay\Actions;

class ConfigAction extends AbstractAction
{
    public function execute(): array
    {
        return [
            'FriendlyName' => [
                'Type' => 'System',
                'Value' => 'Tpay',
            ],
            'clientId' => [
                'FriendlyName' => 'Client ID',
                'Type' => 'text',
                'Size' => '100',
            ],
            'clientSecret' => [
                'FriendlyName' => 'Client Secret',
                'Type' => 'password',
                'Size' => '100',
            ],
            'environment' => [
                'FriendlyName' => 'Environment',
                'Type' => 'dropdown',
                'Options' => [
                    'sandbox' => 'Sandbox',
                    'production' => 'Production',
                ],
            ],
        ];
    }
}