<?php

namespace ClubeDev\PagBank\Domain;

class Address
{
    public function __construct(
        public string $street,
        public string $locality,
        public string $city,
        public string $state_code,
        public string $postal_code,
        public ?string $state = null,
        public ?string $number = null,
        public ?string $complement = null,
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
