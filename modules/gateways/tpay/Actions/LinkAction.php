<?php

namespace JakubFilip\Tpay\Actions;

use JakubFilip\Tpay\Views\TemplateRenderer;

class LinkAction extends AbstractAction
{
    public function __construct(
        protected TemplateRenderer $templateRenderer,
        array $params = []
    )
    {
        parent::__construct($params);
    }

    public function execute(): string
    {
        if ($this->isPaymentFormSubmitted()) {
            return $this->prepareRedirectForm();
        }

        return $this->preparePaymentForm();
    }

    protected function isPaymentFormSubmitted(): bool
    {
        if (!isset($_POST['tpay_payment_token']) || !isset($_SESSION['tpay_payment_token'])) {
            return false;
        }

        return $_POST['tpay_payment_token'] === $_SESSION['tpay_payment_token'];
    }

    protected function preparePaymentForm(): string
    {
        $paymentToken = bin2hex(random_bytes(16));

        $_SESSION['tpay_payment_token'] = $paymentToken;

        return $this->templateRenderer->render('payment_form', [
            'redirectUrl' => $this->params['returnurl'],
            'paymentToken' => $paymentToken,
            'langPayNow' => 'Pay Now',
        ]);
    }

    protected function prepareRedirectForm(): string
    {
        unset($_SESSION['tpay_payment_token']);

        return $this->templateRenderer->render('redirect_form', [
            'redirectUrl' => $this->params['returnurl'],
            'langRedirect' => 'Redirecting to Tpay... Please wait.',
        ]);
    }
}