<?php

namespace ClubeDev\PagBank\Domain;

class Phone
{
    public function __construct(
        private int $country,
        private int $area,
        private int $number
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
