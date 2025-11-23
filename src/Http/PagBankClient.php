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
        $projectRoot = realpath(__DIR__ . '/../../../../../');
        $composerLock = $projectRoot . '/composer.lock';

        if(!file_exists($projectRoot) || !file_exists($composerLock)) {
            throw new ClubedevException('Não conseguimos validar sua biblioteca', 500);
        }

        $this->http = new Client([
            'base_uri' => 'https://clubedev.com.br/api/' . ($sandbox ? 'sandbox/' : ''),
            'timeout'  => 10,
            'headers'  => [
                'X-PAGBANK-TOKEN' => $pagBankToken,
                'X-CLUBEDEV-TOKEN' => $clubeDevToken,
                'X-FINGERPRINT' => hash('sha256', realpath($projectRoot).filemtime($composerLock).phpversion()),
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
                case 'clubedev_depreciated_library':
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
