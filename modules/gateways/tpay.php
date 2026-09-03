<?php

if (!defined('WHMCS')) {
    exit('This file cannot be accessed directly.');
}

function tpay_MetaData(): array
{
    return [
        'DisplayName' => 'Tpay',
        'APIVersion' => '1.1',
    ];
}

function tpay_config(): array
{
    return [
        'FriendlyName' => [
            'Type' => 'System',
            'Value' => 'Tpay',
        ],
    ];
}

function tpay_link(array $params): string
{
    return <<<HTML
<button type="button" class="btn btn-primary">{$params['langpaynow']}</button>
HTML;

}