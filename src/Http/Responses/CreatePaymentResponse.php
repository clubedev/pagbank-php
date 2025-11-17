<?php

namespace ClubeDev\PagBank\Http\Responses;

use ClubeDev\PagBank\Domain\Address;
use ClubeDev\PagBank\Domain\Client;
use ClubeDev\PagBank\Domain\Item;
use ClubeDev\PagBank\Domain\Payment;
use ClubeDev\PagBank\Domain\Payment\CreditCard;
use ClubeDev\PagBank\Domain\Payment\Holder;
use ClubeDev\PagBank\Domain\Payment\Pix;
use ClubeDev\PagBank\Domain\Payment\Title;
use ClubeDev\PagBank\Domain\Phone;
use ClubeDev\PagBank\Domain\Shipping;

class CreatePaymentResponse
{
    public function __construct(private array $data) 
    { }

    public function orderId(): string
    {
        return $this->data['id'];
    }

    public function reference(): mixed
    {
        return $this->data['reference'];
    }

    public function createdAt(): string
    {
        return $this->data['created_at'];
    }

    public function status(): string
    {
        return $this->data['status'];
    }

    public function shipping(): ?Shipping
    {
        return !empty($this->data['shipping']['address']) ? new Shipping(
            address: new Address(
                street: $this->data['shipping']['address']['street'] ?? '',
                number: $this->data['shipping']['address']['number'] ?? '',
                locality: $this->data['shipping']['address']['locality'] ?? '',
                city: $this->data['shipping']['address']['city'] ?? '',
                state_code: $this->data['shipping']['address']['state_code'] ?? '',
                postal_code: $this->data['shipping']['address']['postal_code'] ?? '',
            )
        ) : null;
    }

    public function items(): array
    {
        $items = [];
        foreach($this->data['items'] ?? [] as $item) {
            $items[] = new Item(
                name: $item['name'],
                quantity: $item['quantity'],
                unit_amount: $item['unit_amount'],
            );
        }

        return $items;
    }

    public function client(): ?Client
    {
        $phones = null;
        if(!empty($this->data['client']['phones'])) {
            foreach($this->data['client']['phones'] as $phone) {
                $phones[] = new Phone(
                    country: $phone['country'],
                    area: $phone['area'],
                    number: $phone['number']
                );
            }
        }

        return !empty($this->data['client']) ? new Client(
            document: $this->data['client']['document'],
            name: $this->data['client']['name'],
            email: $this->data['client']['email'],
            phones: $phones
        ) : null;
    }

    public function payment(): ?Payment
    {
        $pix = null;
        if(!empty($this->data['payment']['pix'])) {
            $pix = new Pix(
                expiration_date: $this->data['payment']['pix']['expiration_date'],
                amount: $this->data['payment']['pix']['amount'],
                qrcode: $this->data['payment']['pix']['qrcode'],
                qrcode_image: $this->data['payment']['pix']['qrcode_image']
            );
        }

        $title = null;
        if(!empty($this->data['payment']['title'])) {
            $title = new Title(
                description: $this->data['payment']['title']['description'],
                amount: $this->data['payment']['title']['amount'],
                due_date: $this->data['payment']['title']['due_date'],
                charge_id: $this->data['payment']['title']['charge_id'],
                bar_code: $this->data['payment']['title']['bar_code'],
                url: $this->data['payment']['title']['url'],
                holder: new Holder(
                    name: $this->data['payment']['title']['holder']['name'],
                    email: $this->data['payment']['title']['holder']['email'],
                    document: $this->data['payment']['title']['holder']['document'],
                    address: new Address(
                        street: $this->data['payment']['title']['holder']['address']['street'] ?? '',
                        number: $this->data['payment']['title']['holder']['address']['number'] ?? '',
                        locality: $this->data['payment']['title']['holder']['address']['locality'] ?? '',
                        city: $this->data['payment']['title']['holder']['address']['city'] ?? '',
                        state: $this->data['payment']['title']['holder']['address']['state'] ?? '',
                        state_code: $this->data['payment']['title']['holder']['address']['state_code'] ?? '',
                        postal_code: $this->data['payment']['title']['holder']['address']['postal_code'] ?? '',
                    )
                ),
            );
        }

        $creditCard = null;
        if(!empty($this->data['payment']['credit_card'])) {
            $creditCard = new CreditCard(
                amount: $this->data['payment']['credit_card']['amount'],
                description: $this->data['payment']['credit_card']['description'],
                soft_descriptor: $this->data['payment']['credit_card']['soft_descriptor'],
                charge_id: $this->data['payment']['credit_card']['charge_id'],
                reference: $this->data['payment']['credit_card']['reference'],
                authorization_code: $this->data['payment']['credit_card']['authorization_code'],
                nsu: $this->data['payment']['credit_card']['nsu'],
                brand: $this->data['payment']['credit_card']['brand'],
                card_number: $this->data['payment']['credit_card']['card_number'],
                expiration: $this->data['payment']['credit_card']['expiration'],
                holder: new Holder(
                    name: $this->data['payment']['credit_card']['holder']['name'],
                    document: $this->data['payment']['credit_card']['holder']['document'],
                ),
            );
        }

        return !empty($this->data['payment']) ? new Payment(
            pix: $pix,
            title: $title,
            credit_card: $creditCard,
            paid_at: $this->data['payment']['paid_at'] ?? null
        ) : null;
    }
}
