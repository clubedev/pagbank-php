<?php

namespace ClubeDev\PagBank\Domain;

use ClubeDev\PagBank\Exceptions\ValidationException;

class Client
{
    public function __construct(
        public string $document,
        public string $name,
        public string $email,
        public ?array $phones = null
    ) {
        foreach($this->phones ?? [] as $phone) {
            if(!($phone instanceof Phone)) {
                throw new ValidationException('Cada item em "phones" deve ser uma instância da classe Phone.');
            }
        }
    }

    public function toArray(): array
    {
        return array_filter([
            'document' => $this->document,
            'name' => $this->name,
            'email' => $this->email,
            'phones' => $this->phones,
        ]);
    }
}
