<?php

namespace ClubeDev\PagBank\Domain;

use ClubeDev\PagBank\Domain\Payment\CreditCard;
use ClubeDev\PagBank\Domain\Payment\Pix;
use ClubeDev\PagBank\Domain\Payment\Title;
use ClubeDev\PagBank\Exceptions\ValidationException;

class Payment
{
    public function __construct(
        public ?Pix $pix = null,
        public ?Title $title = null,
        public ?CreditCard $credit_card = null,
        public ?string $paid_at = null,
    ) {
        if(empty($this->pix) && empty($this->title) && empty($this->credit_card)) {
            throw new ValidationException('Informe uma forma de pagamento.');
        }
    }

    public function toArray(): array
    {
        return array_filter([
            'pix' => $this->pix?->toArray(),
            'title' => $this->title?->toArray(),
            'credit_card' => $this->credit_card?->toArray(),
            'paid_at' => $this->paid_at?->toArray(),
        ]);
    }
}
