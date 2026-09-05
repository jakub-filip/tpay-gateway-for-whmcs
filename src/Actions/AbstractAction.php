<?php

namespace JakubFilip\Tpay\Actions;

abstract class AbstractAction
{
    public function __construct(
        protected array $params = []
    ) {}

    abstract public function execute(): array|string;
}