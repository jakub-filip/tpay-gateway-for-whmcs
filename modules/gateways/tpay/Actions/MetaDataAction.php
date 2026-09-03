<?php

namespace JakubFilip\Tpay\Actions;

class MetaDataAction extends AbstractAction
{
    public function execute(): array
    {
        return [
            'DisplayName' => 'Tpay',
            'APIVersion' => '1.1',
        ];
    }
}