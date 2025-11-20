<?php

namespace ClubeDev\PagBank\Domain\Payment;

class Pix
{
    public function __construct(
        public string $expiration_date,
        public float $amount,
        public ?string $charge_id = null,
        public ?string $qrcode = null,
        public ?string $qrcode_image = null
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'expiration_date' => $this->expiration_date,
            'amount' => $this->amount,
            'charge_id' => $this->charge_id,
            'qrcode' => $this->qrcode,
            'qrcode_image' => $this->qrcode_image,
        ]);
    }
}
