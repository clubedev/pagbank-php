<?php

namespace ClubeDev\PagBank\Domain;

class Item
{
    public function __construct(
        private string $name,
        private int $quantity,
        private float $unit_amount
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'quantity' => $this->quantity,
            'unit_amount' => $this->unit_amount,
        ]);
    }
}
