<?php

namespace miraliog\pelegram\Concerns\Accessors;

trait PaymentAccessor
{
    public function preCheckoutQueryId(): ?string
    {
        return $this->raw['pre_checkout_query']['id'] ?? null;
    }
    public function successfulPayment(): ?array
    {
        return $this->raw['message']['successful_payment'] ?? null;
    }
    public function successfulPaymentPayload(): ?string
    {
        return $this->raw['message']['successful_payment']['invoice_payload'] ?? null;
    }
}
