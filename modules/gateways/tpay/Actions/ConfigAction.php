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
        ];
    }
}