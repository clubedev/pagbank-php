<?php

namespace ClubeDev\PagBank\Domain;

class Address
{
    public function __construct(
        private string $street,
        private ?string $number,
        private ?string $complement,
        private string $locality,
        private string $city,
        private string $state,
        private string $state_code,
        private string $postal_code,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'street' => $this->street,
            'number' => $this->number,
            'complement' => $this->complement,
            'locality' => $this->locality,
            'city' => $this->city,
            'state' => $this->state,
            'state_code' => $this->state_code,
            'postal_code' => $this->postal_code,
        ]);
    }
}
