<?php 

require 'vendor/autoload.php';

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
use ClubeDev\PagBank\Exceptions\ClubedevException;
use ClubeDev\PagBank\Exceptions\PagBankException;
use ClubeDev\PagBank\PagBank;

$payment = new PagBank(
    pagBankToken: '32915459-ad24-4c77-9d80-31dc555bf679c9515a4e4990b187914bcef074c6f4f8fed7-d536-4df8-a41a-b180fde935c9',
    clubeDevToken: '78e670d6-bbbc-4cec-a4e8-79556aa61dfc',
    sandbox: true
);

try {
    $response = $payment->createPayment(
        reference: 'a75bfd24-0a0e-4fff-92a9-98c6dd290185',
        client: new Client(
            document: '123.456.789-09',
            name: 'Wanderson Teste',
            email: 'wanderson@teste.com',
            phones: [ // Opcional
                new Phone(country: 55, area: 21, number: 999121314),
                new Phone(country: 55, area: 21, number: 999151617),
            ]
        ),
        items: [
            new Item(
                name: 'Item teste 01',
                quantity: 1,
                unit_amount: 12.99
            ),
            new Item(
                name: 'Item teste 02',
                quantity: 1,
                unit_amount: 18.39
            )
        ],
        shipping: new Shipping(
            address: new Address(
                street: 'Rua 01',
                number: '01',
                complement: 'Q01 L01',
                locality: 'Bairro',
                city: 'Cidade',
                state_code: 'SP',
                postal_code: '01310930'
            )
        ),
        payment: new Payment(
            // pix: new Pix(
            //     expiration_date: '2025-11-17 13:30:00',
            //     amount: 16.99
            // ),
            // title: new Title(
            //     description: 'Descrição do boleto',
            //     amount: 14.98,
            //     due_date: '2025-11-20',
            //     holder: new Holder(
            //         document: '123.456.789-09',
            //         name: 'Wanderson Teste',
            //         email: 'wanderson@teste.com',
            //         address: new Address(
            //             street: 'Rua 01',
            //             number: '01',
            //             locality: 'Bairro',
            //             city: 'Cidade',
            //             state: 'São Paulo',
            //             state_code: 'SP',
            //             postal_code: '01310930'
            //         )
            //     )
            // ),
            credit_card: new CreditCard(
                amount: 21.36,
                description: 'Descrição do pagamento',
                soft_descriptor: 'NOMEFATURA',
                card_token: 'dsouM99fH1XWK/FG5adPlap5JYe5MCF9xXEYrZ0KTSzNolXg9NbhHVCduLgdBPu4QKzQRMr+yzdrrEwPG72/ekNBQdoHaOCMR0L7VG7h1PKRmfpkiOUEssoMSR88tswExWGTkqwDLl1Fc+phwLMF1K4hN7OMrn3bdDss9NK0gb0jX4aWWEmUNN2KY/PKFqqONfDOK6tsSzTTul0af1ukhiJ2b1H2M++hksH4O3BDGT/NewVQL6jZTlDdBdnD1gDGyYL/3F97tK93wh59QqcJZLLJsaDz3rHSeHRn6PhGbtFZ3oLTjO2fg94yMkZmbT3U0Qa/Kfx58n0zCKPpgj6UXg==',
                holder: new Holder(
                    document: '123.456.789-09',
                    name: 'Wanderson Teste',
                )
            )
        ),
    );

    echo '---------------------------------------------------------';
    echo json_encode($response->payment()?->credit_card?->toArray());
    echo '---------------------------------------------------------';
} catch(ClubedevException $e) {
    $exception = json_decode($e->getMessage());
    echo $exception?->error ?? $exception?->message ?? $e->getMessage();
} catch(PagBankException $e) {
    $exception = json_decode($e->getMessage());
    echo $exception?->error ?? $exception?->message ?? $e->getMessage();
} catch (\Throwable $th) {
    throw $th;
}

// MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAr+ZqgD892U9/HXsa7XqBZUayPquAfh9xx4iwUbTSUAvTlmiXFQNTp0Bvt/5vK2FhMj39qSv1zi2OuBjvW38q1E374nzx6NNBL5JosV0+SDINTlCG0cmigHuBOyWzYmjgca+mtQu4WczCaApNaSuVqgb8u7Bd9GCOL4YJotvV5+81frlSwQXralhwRzGhj/A57CGPgGKiuPT+AOGmykIGEZsSD9RKkyoKIoc0OS8CPIzdBOtTQCIwrLn2FxI83Clcg55W8gkFSOS6rWNbG5qFZWMll6yl02HtunalHmUlRUL66YeGXdMDC2PuRcmZbGO5a/2tbVppW6mfSWG3NPRpgwIDAQAB