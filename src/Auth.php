<?php

namespace ClubeDev\PagBank;

use GuzzleHttp\Client;
use ClubeDev\PagBank\Exceptions\PagBankException;

class Auth
{
    protected Client $client;

    public function __construct(
        protected string $clientId,
        protected string $clientSecret
    ) {
        $this->client = new Client([
            'base_uri' => 'https://api.pagbank.com.br/oauth2/',
            'timeout'  => 10,
        ]);
    }

    public function authenticate(): string
    {
        try {
            $response = $this->client->post('token', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if (!isset($data['access_token'])) {
                throw new PagBankException('Access token não retornado pelo PagBank.');
            }

            return $data['access_token'];
        } catch (\Exception $e) {
            throw new PagBankException('Erro ao autenticar no PagBank: ' . $e->getMessage());
        }
    }
}
