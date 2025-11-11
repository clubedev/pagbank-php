<?php

namespace ClubeDev\PagBank\Domain\Payment;

class Pix
{
    public function __construct(
        private string $expiration_date,
        private float $amount
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'expiration_date' => $this->expiration_date,
            'amount' => $this->amount,
        ]);
    }
}
