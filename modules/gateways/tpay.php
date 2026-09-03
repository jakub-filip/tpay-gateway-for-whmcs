<?php

if (!defined('WHMCS')) {
    exit('This file cannot be accessed directly.');
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'tpay' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use JakubFilip\Tpay\Actions\MetaDataAction;
use JakubFilip\Tpay\Actions\ConfigAction;
use JakubFilip\Tpay\Actions\LinkAction;

function tpay_MetaData(): array
{
    return (new MetaDataAction())->execute();
}

function tpay_config(): array
{
    return (new ConfigAction())->execute();
}

function tpay_link(array $params): string
{
    return (new LinkAction($params))->execute();
}