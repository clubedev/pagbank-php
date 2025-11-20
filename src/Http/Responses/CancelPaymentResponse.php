<?php

namespace ClubeDev\PagBank\Http\Responses;

class CancelPaymentResponse
{
    public function __construct(private array $data) 
    { }

    public function raw(): array
    {
        return $this->data;
    }

    public function canceled(): bool
    {
        return $this->data['canceled'];
    }

    public function id(): mixed
    {
        return $this->data['id'];
    }

    public function fullRefunded(): bool
    {
        return $this->data['full_refunded'];
    }

    public function paid(): float
    {
        return $this->data['paid'];
    }

    public function refunded(): float
    {
        return $this->data['refunded'];
    }
}
