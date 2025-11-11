<?php

namespace ClubeDev\PagBank\Http\Requests;

class CreatePaymentRequest
{
    public function __construct(
        protected array $data
    ) {}

    public function toArray(): array
    {
        // Aqui você pode validar os campos mínimos exigidos.
        return $this->data;
    }
}
