<?php

namespace ClubeDev\PagBank;

use ClubeDev\PagBank\Http\PagBankClient;
use ClubeDev\PagBank\Http\Requests\CreatePaymentRequest;
use ClubeDev\PagBank\Domain\Client;
use ClubeDev\PagBank\Domain\Payment;
use ClubeDev\PagBank\Domain\Shipping;
use ClubeDev\PagBank\Http\Responses\CancelPaymentResponse;
use ClubeDev\PagBank\Http\Responses\CreatePaymentResponse;
use ClubeDev\PagBank\Http\Responses\GetPaymentResponse;
use ClubeDev\PagBank\Http\Webhooks\PaymentWebhook;

class PagBank
{
    protected PagBankClient $client;
    private string $pagBankToken;

    public function __construct(string $pagBankToken, string $clubeDevToken, bool $sandbox = false)
    {
        $this->pagBankToken = $pagBankToken;
        $this->client = new PagBankClient($pagBankToken, $clubeDevToken, $sandbox);
    }

    public function createPayment(mixed $reference, Client $client, Payment $payment, ?array $items = null, ?Shipping $shipping = null, ?string $webhook_url = null): CreatePaymentResponse
    {
        return new CreatePaymentResponse($this->client->post('libraries/pagbank/payment', new CreatePaymentRequest(
            reference: $reference,
            client: $client,
            payment: $payment,
            items: $items,
            shipping: $shipping,
            webhook_url: $webhook_url
        )->toArray()));
    }

    public function getPayment(mixed $orderId): GetPaymentResponse
    {
        return new GetPaymentResponse($this->client->get("libraries/pagbank/payment/{$orderId}"));
    }

    public function cancelPayment(mixed $chargeId, float $amount): CancelPaymentResponse
    {
        return new CancelPaymentResponse($this->client->delete("libraries/pagbank/payment/{$chargeId}", ['amount' => $amount]));
    }

    public function webhook(array $data): PaymentWebhook
    {
        return new PaymentWebhook($data, $this->pagBankToken);
    }
}
