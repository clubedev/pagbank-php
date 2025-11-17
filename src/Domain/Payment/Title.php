<?php

namespace ClubeDev\PagBank\Domain\Payment;

class Title
{
    public function __construct(
        public string $description,
        public float $amount,
        public string $due_date,
        public Holder $holder,
        public ?string $instruction_line_1 = null,
        public ?string $instruction_line_2 = null,
        public ?string $charge_id = null,
        public ?string $bar_code = null,
        public ?string $url = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'description' => $this->description,
            'amount' => $this->amount,
            'due_date' => $this->due_date,
            'instruction_line_1' => $this->instruction_line_1,
            'instruction_line_2' => $this->instruction_line_2,
            'charge_id' => $this->charge_id,
            'bar_code' => $this->bar_code,
            'url' => $this->url,
            'holder' => $this->holder->toArray(),
        ]);
    }
}
