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
        reference: 'a75bfd24-0a0e-4fff-92a9-98c6dd290185', // Obrigatório
        client: new Client( // Obrigatório
            document: '123.456.789-09', // Obrigatório
            name: 'Wanderson Teste', // Obrigatório
            email: 'wanderson@teste.com', // Obrigatório
            phones: [ // Opcional
                new Phone(
                    country: 55, // Obrigatório para esta classe
                    area: 21, // Obrigatório para esta classe
                    number: 999121314 // Obrigatório para esta classe
                ),
                new Phone(
                    country: 55, // Obrigatório para esta classe 
                    area: 21, // Obrigatório para esta classe 
                    number: 999151617 // Obrigatório para esta classe
                ),
            ]
        ),
        items: [ // Opcional
            new Item(
                name: 'Item teste 01', // Obrigatório para esta classe
                quantity: 1, // Obrigatório para esta classe
                unit_amount: 12.99 // Obrigatório para esta classe
            ),
            new Item(
                name: 'Item teste 02', // Obrigatório para esta classe
                quantity: 1, // Obrigatório para esta classe
                unit_amount: 18.39 // Obrigatório para esta classe
            )
        ],
        shipping: new Shipping( // Opcional
            address: new Address( // Obrigatório caso o Shipping for informado
                street: 'Rua 01', // Obrigatório para esta classe
                number: '01', // Opcional
                complement: 'Q01 L01', // Opcional
                locality: 'Bairro', // Obrigatório para esta classe
                city: 'Cidade', // Obrigatório para esta classe
                state_code: 'SP', // Obrigatório para esta classe
                postal_code: '01310930' // Obrigatório para esta classe
            )
        ),
        payment: new Payment( // Obrigatório (também é obrigatório enviar um tipo de pagamento)
            pix: new Pix(
                expiration_date: '2025-11-20 13:30:00', // Obrigatório para esta classe
                amount: 16.99 // Obrigatório para esta classe
            ),
            title: new Title(
                description: 'Descrição do boleto', // Obrigatório para esta classe
                amount: 14.98, // Obrigatório para esta classe
                due_date: '2025-11-20', // Obrigatório para esta classe
                holder: new Holder(
                    document: '123.456.789-09', // Obrigatório para esta classe
                    name: 'Wanderson Teste', // Obrigatório para esta classe
                    email: 'wanderson@teste.com', // Obrigatório para esta classe
                    address: new Address(
                        street: 'Rua 01', // Obrigatório para esta classe
                        number: '01', // Opcional
                        locality: 'Bairro', // Obrigatório para esta classe
                        city: 'Cidade', // Obrigatório para esta classe
                        state: 'São Paulo', // Obrigatório para esta classe
                        state_code: 'SP', // Obrigatório para esta classe
                        postal_code: '01310930' // Obrigatório para esta classe
                    )
                )
            ),
            credit_card: new CreditCard(
                amount: 21.36, // Obrigatório para esta classe
                description: 'Descrição do pagamento', // Obrigatório para esta classe
                soft_descriptor: 'NOMEFATURA', // Obrigatório para esta classe
                card_token: 'dsouM99fH1XWK/FG5adPlap5JYe5MCF9xXEYrZ0KTSzNolXg9NbhHVCduLgdBPu4QKzQRMr+yzdrrEwPG72/ekNBQdoHaOCMR0L7VG7h1PKRmfpkiOUEssoMSR88tswExWGTkqwDLl1Fc+phwLMF1K4hN7OMrn3bdDss9NK0gb0jX4aWWEmUNN2KY/PKFqqONfDOK6tsSzTTul0af1ukhiJ2b1H2M++hksH4O3BDGT/NewVQL6jZTlDdBdnD1gDGyYL/3F97tK93wh59QqcJZLLJsaDz3rHSeHRn6PhGbtFZ3oLTjO2fg94yMkZmbT3U0Qa/Kfx58n0zCKPpgj6UXg==', // Card token será gerado por outra biblioteca do clube dev que vai estar no npm e será grátis
                holder: new Holder(
                    document: '123.456.789-09', // Obrigatório para esta classe
                    name: 'Wanderson Teste', // Obrigatório para esta classe
                )
            )
        ),
        webhook_url: ['https://lazy-sparrow-06.webhook.cool'] // Opcional
    );

    // $response = $payment->getPayment('ORDE_DF4EDFCC-ED9D-4299-BE43-2AC21A7823BF');

    /****************************
     * Processamento de webhook *
     ****************************/
    $response = $payment->cancelPayment('CHAR_2C20465F-E475-4340-9BE0-C9461D5FF7CC', 1.29);

    /****************************
     * Processamento de webhook *
     ****************************/
    $paymentWebhook = $payment->webhook([
        "id" => "ORDE_DF4EDFCC-ED9D-4299-BE43-2AC21A7823BF",
        "reference_id" => "a75bfd24-0a0e-4fff-92a9-98c6dd290185",
        "created_at" => "2025-11-19T19:26:24.755-03:00",
        "customer" => [
            "name" => "Wanderson Teste",
            "email" => "wanderson@teste.com",
            "tax_id" => "12345678909",
            "phones" => [
                [
                    "type" => "MOBILE",
                    "country" => "55",
                    "area" => "21",
                    "number" => "999121314"
                ],
                [
                    "type" => "MOBILE",
                    "country" => "55",
                    "area" => "21",
                    "number" => "999151617"
                ]
            ]
        ],
        "items" => [
            [
                "name" => "Item teste 01",
                "quantity" => 1,
                "unit_amount" => 1299
            ],
            [
                "name" => "Item teste 02",
                "quantity" => 1,
                "unit_amount" => 1839
            ]
        ],
        "shipping" => [
            "address" => [
                "street" => "Rua 01",
                "number" => "01",
                "complement" => "Q01 L01",
                "locality" => "Bairro",
                "city" => "Cidade",
                "region_code" => "SP",
                "country" => "BRA",
                "postal_code" => "01310930"
            ]
        ],
        "qr_codes" => [
            [
                "id" => "QRCO_2C20465F-E475-4340-9BE0-C9461D5FF7CC",
                "expiration_date" => "2025-11-20T13:30:00.000-03:00",
                "amount" => [
                    "value" => 1699
                ],
                "text" => "00020101021226850014br.gov.bcb.pix2563api-h.pagseguro.com/pix/v2/2C20465F-E475-4340-9BE0-C9461D5FF7CC27600016BR.COM.PAGSEGURO01362C20465F-E475-4340-9BE0-C9461D5FF7CC520489995303986540516.995802BR5922Martha Manuella de Sou6015Aparecida de Go62070503***630464D3",
                "arrangements" => ["PIX"],
                "links" => [
                    [
                        "rel" => "QRCODE.PNG",
                        "href" => "https://sandbox.api.pagseguro.com/qrcode/QRCO_2C20465F-E475-4340-9BE0-C9461D5FF7CC/png",
                        "media" => "image/png",
                        "type" => "GET"
                    ],
                    [
                        "rel" => "QRCODE.BASE64",
                        "href" => "https://sandbox.api.pagseguro.com/qrcode/QRCO_2C20465F-E475-4340-9BE0-C9461D5FF7CC/base64",
                        "media" => "text/plain",
                        "type" => "GET"
                    ]
                ]
            ]
        ],
        "charges" => [
            [
                "id" => "CHAR_2C20465F-E475-4340-9BE0-C9461D5FF7CC",
                "reference_id" => "a75bfd24-0a0e-4fff-92a9-98c6dd290185",
                "status" => "PAID",
                "created_at" => "2025-11-19T19:26:41.982-03:00",
                "paid_at" => "2025-11-19T19:26:43.780-03:00",
                "amount" => [
                    "value" => 1699,
                    "currency" => "BRL",
                    "summary" => [
                        "total" => 1699,
                        "paid" => 1699,
                        "refunded" => 0,
                        "incremented" => 0
                    ]
                ],
                "payment_response" => [
                    "code" => "20000",
                    "message" => "SUCESSO"
                ],
                "payment_method" => [
                    "type" => "PIX",
                    "pix" => [
                        "notification_id" => "NTF_683ECEE6-5B1C-4650-A27C-F05BB7B01070",
                        "end_to_end_id" => "4294b1dcdf8e4bfba4a1ca73ae866a91",
                        "holder" => [
                            "name" => "API-PIX Payer Mock",
                            "tax_id" => "***931180**"
                        ]
                    ]
                ],
                "links" => [
                    [
                        "rel" => "SELF",
                        "href" => "https://internal.sandbox.api.pagseguro.com/charges/CHAR_2C20465F-E475-4340-9BE0-C9461D5FF7CC",
                        "media" => "application/json",
                        "type" => "GET"
                    ],
                    [
                        "rel" => "CHARGE.CANCEL",
                        "href" => "https://internal.sandbox.api.pagseguro.com/charges/CHAR_2C20465F-E475-4340-9BE0-C9461D5FF7CC/cancel",
                        "media" => "application/json",
                        "type" => "POST"
                    ]
                ],
                "metadata" => []
            ]
        ],
        "notification_urls" => [
            "https://lazy-sparrow-06.webhook.cool"
        ],
        "links" => [
            [
                "rel" => "SELF",
                "href" => "https://sandbox.api.pagseguro.com/orders/ORDE_DF4EDFCC-ED9D-4299-BE43-2AC21A7823BF",
                "media" => "application/json",
                "type" => "GET"
            ],
            [
                "rel" => "PAY",
                "href" => "https://sandbox.api.pagseguro.com/orders/ORDE_DF4EDFCC-ED9D-4299-BE43-2AC21A7823BF/pay",
                "media" => "application/json",
                "type" => "POST"
            ]
        ]
    ]);

    echo "---------------------------------------------------------\n\n";
    echo json_encode($paymentWebhook->payment()->toArray());
    echo "\n\n---------------------------------------------------------";
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