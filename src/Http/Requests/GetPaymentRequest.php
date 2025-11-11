<?php

namespace ClubeDev\PagBank\Http\Requests;

class GetPaymentRequest
{
    public string $orderId;

    public function __construct(string $orderId)
    {
        $this->orderId = $orderId;
    }
}
