<?php

namespace ClubeDev\PagBank\Http;

use GuzzleHttp\Client;
use ClubeDev\PagBank\Exceptions\PagBankException;

class PagBankClient
{
    protected Client $http;

    public function __construct(protected string $pagBankToken, protected string $clubeDevToken, protected bool $sandbox = false) 
    {
        $this->http = new Client([
            'base_uri' => 'http://localhost:8000/api' . ($this->sandbox ? '/sandbox' : ''),
            'timeout'  => 10,
            'headers'  => [
                'X-PAGBANK-TOKEN' => $this->pagBankToken,
                'X-CLUBEDEV-TOKEN' => $this->clubeDevToken,
                'Accept' => 'application/json',
            ]
        ]);
    }

    public function post(string $endpoint, array $data): array
    {
        try {
            $response = $this->http->post($endpoint, ['json' => $data]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Throwable $e) {
            throw new PagBankException($e->getMessage(), $e->getCode());
        }
    }

    public function get(string $endpoint): array
    {
        try {
            $response = $this->http->get($endpoint);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Throwable $e) {
            throw new PagBankException($e->getMessage(), $e->getCode());
        }
    }
}
