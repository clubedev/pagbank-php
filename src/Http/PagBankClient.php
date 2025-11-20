<?php

namespace ClubeDev\PagBank\Http;

use ClubeDev\PagBank\Exceptions\ClubedevException;
use GuzzleHttp\Client;
use ClubeDev\PagBank\Exceptions\PagBankException;
use GuzzleHttp\Exception\ClientException;

class PagBankClient
{
    protected Client $http;

    public function __construct(string $pagBankToken, string $clubeDevToken, bool $sandbox = false)
    {
        $this->http = new Client([
            'base_uri' => 'http://localhost:8000/api/' . ($sandbox ? 'sandbox/' : ''),
            'timeout'  => 10,
            'headers'  => [
                'X-PAGBANK-TOKEN' => $pagBankToken,
                'X-CLUBEDEV-TOKEN' => $clubeDevToken,
                'Accept' => 'application/json',
            ]
        ]);
    }

    public function post(string $endpoint, array $data): array
    {
        try {
            $response = $this->http->post($endpoint, ['json' => $data]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $e) {
            $body = (string) $e->getResponse()->getBody();
            $json = json_decode($body, true);
            switch ($json['type'] ?? null) {
                case 'clubedev_token_not_found':
                    throw new ClubedevException($body, $e->getCode());
                    break;

                default:
                    throw new PagBankException($body, $e->getCode());
            }
        }

        return [];
    }

    public function get(string $endpoint, array $data = []): array
    {
        try {
            $response = $this->http->get($endpoint, ['query' => $data]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $e) {
            $body = (string) $e->getResponse()->getBody();
            $json = json_decode($body, true);
            switch ($json['type'] ?? null) {
                case 'clubedev_token_not_found':
                    throw new ClubedevException($body, $e->getCode());
                    break;

                default:
                    throw new PagBankException($body, $e->getCode());
            }
        }
    }

    public function delete(string $endpoint, array $data = []): array
    {
        try {
            $response = $this->http->delete($endpoint, ['query' => $data]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $e) {
            $body = (string) $e->getResponse()->getBody();
            $json = json_decode($body, true);
            switch ($json['type'] ?? null) {
                case 'clubedev_token_not_found':
                    throw new ClubedevException($body, $e->getCode());
                    break;

                default:
                    throw new PagBankException($body, $e->getCode());
            }
        }
    }
}
