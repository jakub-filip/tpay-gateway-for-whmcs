<?php

namespace JakubFilip\Tpay\Views;

use Exception;

class TemplateRenderer
{
    public function __construct(
        protected string $templatesDir
    ) {}

    public function render(string $template, array $params = []): string
    {
        $template = $this->templatesDir . $template . ".html";

        if (!file_exists($template) || !is_readable($template)) {
            throw new Exception('Template file not found: ' . $template);
        }

        $html = file_get_contents($template);

        foreach ($params as $key => $value) {
            $html = str_replace('[#' . $key . '#]', $value, $html);
        }

        return $html;
    }
}