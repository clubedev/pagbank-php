<?php

namespace ClubeDev\PagBank\Domain\Payment;

class CreditCard
{
    public function __construct(
        public float $amount,
        public string $description,
        public string $soft_descriptor,
        public Holder $holder,
        public ?string $card_token = null,
        public int $installments = 1,
        public ?string $charge_id = null,
        public ?string $reference = null,
        public ?string $authorization_code = null,
        public ?string $nsu = null,
        public ?string $brand = null,
        public ?string $card_number = null,
        public ?string $expiration = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'amount' => $this->amount,
            'installments' => $this->installments,
            'description' => $this->description,
            'soft_descriptor' => $this->soft_descriptor,
            'card_token' => $this->card_token,
            'holder' => $this->holder,
            'charge_id' => $this->charge_id,
            'reference' => $this->reference,
            'authorization_code' => $this->authorization_code,
            'nsu' => $this->nsu,
            'brand' => $this->brand,
            'card_number' => $this->card_number,
            'expiration' => $this->expiration,
        ]);
    }
}
