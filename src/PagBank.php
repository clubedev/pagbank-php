<?php

namespace ClubeDev\PagBank;

use ClubeDev\PagBank\Http\PagBankClient;
use ClubeDev\PagBank\Http\Requests\CreatePaymentRequest;
use ClubeDev\PagBank\Domain\Client;
use ClubeDev\PagBank\Domain\Payment;
use ClubeDev\PagBank\Domain\Shipping;
use ClubeDev\PagBank\Http\Responses\CreatePaymentResponse;

class PagBank
{
    protected PagBankClient $client;

    public function __construct(string $pagBankToken, string $clubeDevToken, bool $sandbox = false)
    {
        $this->client = new PagBankClient($pagBankToken, $clubeDevToken, $sandbox);
    }

    public function createPayment(mixed $reference, Client $client, Payment $payment, ?array $items = null, ?Shipping $shipping = null, ?array $webhooks_urls = null): CreatePaymentResponse
    {
        return new CreatePaymentResponse($this->client->post('libraries/pagbank/payment', new CreatePaymentRequest(
            reference: $reference,
            client: $client,
            payment: $payment,
            items: $items,
            shipping: $shipping,
            webhooks_urls: $webhooks_urls
        )->toArray()));
    }

    public function getPayment(mixed $orderId): array
    {
        return $this->client->get("libraries/pagbank/payment/{$orderId}");
    }
}
