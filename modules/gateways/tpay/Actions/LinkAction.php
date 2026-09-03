<?php

namespace JakubFilip\Tpay\Actions;

class LinkAction extends AbstractAction
{
    public function execute(): string
    {
        return <<<HTML
<button type="button" class="btn btn-primary">{$this->params['langpaynow']}</button>
HTML;
    }
}