<?php

namespace ClubeDev\PagBank;

use ClubeDev\PagBank\Http\PagBankClient;
use ClubeDev\PagBank\Http\Requests\CreatePaymentRequest;
use ClubeDev\PagBank\Http\Requests\GetPaymentRequest;

class PagBank
{
    protected PagBankClient $client;

    public function __construct(protected string $pagBankToken, protected string $clubeDevToken, protected bool $sandbox = false)
    {
        $this->client = new PagBankClient($pagBankToken, $clubeDevToken, $sandbox);
    }

    public function createPayment(array $data): array
    {
        $request = new CreatePaymentRequest($data);
        return $this->client->post('/libraries/pagbank/payment', $request->toArray());
    }

    public function getPayment(string $orderId): array
    {
        $request = new GetPaymentRequest($orderId);
        return $this->client->get("/libraries/pagbank/payment/{$request->orderId}");
    }
}
