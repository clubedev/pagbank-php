<?php

namespace ClubeDev\PagBank\Http\Requests;

use ClubeDev\PagBank\Domain\Client;
use ClubeDev\PagBank\Domain\Item;
use ClubeDev\PagBank\Domain\Payment;
use ClubeDev\PagBank\Domain\Shipping;
use ClubeDev\PagBank\Exceptions\ValidationException;

class CreatePaymentRequest
{
    public function __construct(
        private mixed $reference,
        private Client $client,
        private Payment $payment,
        private ?array $items = null,
        private ?Shipping $shipping = null,
        private ?string $webhook_url = null
    ) {
        foreach($this->items ?? [] as $item) {
            if(!($item instanceof Item)) {
                throw new ValidationException('Cada item em "items" deve ser uma instância da classe Item.');
            }
        }
    }

    public function toArray(): array
    {
        $createPayment = [];

        if(!empty($this->items)) {
            $createPayment['items'] = array_map(function(Item $item) { 
                return $item->toArray(); 
            }, $this->items);
        }

        return array_filter([
            'reference' => $this->reference,
            'client' => $this->client->toArray(),
            'payment' => $this->payment?->toArray(),
            'items' => !empty($this->items) ? array_map(fn(Item $item) => $item->toArray(), $this->items) : null,
            'shipping' => $this->shipping?->toArray(),
            'webhooks_urls' => [$this->webhook_url],
        ]);
    }
}
