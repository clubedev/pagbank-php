<?php

namespace ClubeDev\PagBank\Domain;

class Phone
{
    public function __construct(
        public int $country,
        public int $area,
        public int $number
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'country' => $this->country,
            'area' => $this->area,
            'number' => $this->number,
        ]);
    }
}
