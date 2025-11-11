<?php

namespace ClubeDev\PagBank\Services;

use GuzzleHttp\Client;
use ClubeDev\PagBank\Exceptions\PagBankException;

class PaymentService
{
    protected Client $client;

    public function __construct(protected string $pagBankToken, protected string $clubeDevToken, protected bool $sandbox = false) 
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost:8000/api' . ($this->sandbox ? '/sandbox' : ''),
            'timeout'  => 10,
            'headers'  => [
                'X-PAGBANK-TOKEN' => $this->pagBankToken,
                'X-CLUBEDEV-TOKEN' => $this->clubeDevToken,
                'Accept' => 'application/json',
            ]
        ]);
    }

    public function create(array $payload): array
    {
        try {
            $response = $this->client->post('payments', ['json' => $payload]);
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            throw new PagBankException('Erro ao criar pagamento: ' . $e->getMessage());
        }
    }
}
