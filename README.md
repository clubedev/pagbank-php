# PHP PagBank — Integração com PagBank

Este SDK facilita pagamentos com PagBank utilizando a ponte oficial do **ClubeDev**, permitindo criar cobranças, consultar transações e realizar cancelamentos totais ou parciais de maneira simples e padronizada.

A biblioteca foi desenvolvida para ser utilizada por **programadores iniciantes e intermediários**, com foco em simplicidade, clareza e segurança.

---

# Sumário
- [Instalação](#instalação)
- [Requisitos](#requisitos)
- [Autenticação e Tokens](#autenticação-e-tokens)
- [Inicialização](#inicialização)
- [Pagamentos](#pagamentos)
  - [Criar pagamento Pix](#criar-pagamento-pix)
  - [Criar pagamento Cartão](#criar-pagamento-cartão)
  - [Criar pagamento Boleto](#criar-pagamento-boleto)
  - [Consultar pagamento](#consultar-pagamento)
- [Cancelamentos](#cancelamentos)
  - [Cancelamento parcial](#cancelamento-parcial)
  - [Cancelamento total](#cancelamento-total)
- [Processamento Webhook](#processamento-webhook)
- [Domínios (Domains)](#domínios-domains)
- [Tratamento de Exceções](#tratamento-de-exceções)
- [Exemplos completos](#exemplos-completos)
- [Suporte](#suporte)

---

# Instalação

```bash
composer require clubedev/pagbank-php
```

---

# Requisitos

- PHP **8.1+**
- cURL ativado
- Composer

---

# Autenticação e Tokens

Para utilizar a biblioteca, você precisa fornecer:

- **Token PagBank**  
- **Token ClubeDev**

Ambos são obrigatórios para validação, segurança e auditoria das operações.

### Adquirindo e configurando seu Token ClubeDev

#### Ambiente Sandbox (testes)
1. Utilize o seguinte token: `78e670d6-bbbc-4cec-a4e8-79556aa61dfc`

#### Ambiente Produção
1. Acesse: https://clubedev.com.br
2. Compre seu token
3. Acesse sua conta em: https://painel.clubedev.com.br
4. Vá ao menu **Tokens**
5. Clique em **Configurar Token**
6. Preencha os dados e clique em **Salvar**

### Gerando seu Token PagBank

#### Ambiente Sandbox (testes)
1. Acesse: https://portaldev.pagbank.com.br/
2. Crie sua conta de desenvolvedor
3. Vá até o menu **Tokens**
4. Gere o token de sandbox

#### Ambiente Produção
1. Faça login em: https://acesso.pagbank.com.br/
2. Acesse diretamente o painel de integrações:  
   https://minhaconta.pagbank.com.br/venda-online/integracoes/configuracoes
3. Clique em **Gerar Token**

---

# Inicialização

```php
require 'vendor/autoload.php';

use ClubeDev\PagBank\PagBank;

$payment = new PagBank(
    pagBankToken: 'SEU_TOKEN_PAGBANK',
    clubeDevToken: 'SEU_TOKEN_CLUBEDEV',
    sandbox: true // true = ambiente de testes
);
```

---

# Pagamentos

## Criar pagamento Pix

```php
use ClubeDev\PagBank\Domain\Client;
use ClubeDev\PagBank\Domain\Phone;
use ClubeDev\PagBank\Domain\Item;
use ClubeDev\PagBank\Domain\Shipping;
use ClubeDev\PagBank\Domain\Address;
use ClubeDev\PagBank\Domain\Payment;
use ClubeDev\PagBank\Domain\Payment\Pix;

$response = $payment->createPayment(
    reference: "identificação-informada-por-você", // Geralmente utilizado algum id
    client: new Client(
        document: "12345678909", 
        name: "João", 
        email: "email@email.com",
        phones: [ // Opcional
            new Phone(
                country: 55,
                area: 21,
                number: 999121314
            ),
            new Phone(
                country: 55, 
                area: 21, 
                number: 999151617
            ),
        ]
    ),
    items: [ // Opcional
        new Item(
            name: 'Item X',
            quantity: 1,
            unit_amount: 12.99
        )
    ],
    shipping: new Shipping( // Opcional
        address: new Address(
            street: 'Rua 01',
            number: '01', // Opcional
            complement: 'Q01 L01', // Opcional
            locality: 'Bairro',
            city: 'Cidade',
            state_code: 'SP',
            postal_code: '01310930'
        )
    ),
    payment: new Payment(
        pix: new Pix(
            expiration_date: "2025-12-01 12:00:00",
            amount: 25.90
        )
    ),
    webhook_url: 'https://exemplo.com/webhook' // Opcional
);

```

### Retorno esperado

Método | Tipo Retorno | Descrição
--- | --- | ---
$response->raw() | Array | Este método retorna os dados brutos
$response->orderId() | String | ID do pedido PagBank
$response->reference() | Mixed | Referência enviada por você ao PagBank
$response->createdAt() | String | Data de criação do pagamento, formato: Y-m-d H:i:s
$response->status() | String | Status do pagamento
$response->shipping() | Shipping* ou null | Caso tenha enviado dados de entrega, será retornado aqui
$response->items() | Array de Item* | Caso tenha enviado items, será retornado aqui
$response->client() | Client* | Dados do cliente que irá realizar o pagamento
$response->payment() | Payment* | Dados do pagamento

\* Shipping, Item, Client e Payment são domain para tratamento de dados, no fim da documentação terá os detalhes de cada um 

### Exemplo de uso do retorno
```php
$amount = $response->payment()?->pix?->amount;
$expirationDate = $response->payment()?->pix?->expiration_date;
$chargeId = $response->payment()?->pix?->charge_id;
$qrcode = $response->payment()?->pix?->qrcode;
$qrcodeImage = $response->payment()?->pix?->qrcode_image;
```

---

## Criar pagamento Cartão

Para criar pagamento com cartão é obrigatório a utilização da biblioteca frontend **[@clubedev/pagbank-encrypt-card](https://www.npmjs.com/package/@clubedev/pagbank-encrypt-card)** para gerar o **card_token**

```php
use ClubeDev\PagBank\Domain\Client;
use ClubeDev\PagBank\Domain\Phone;
use ClubeDev\PagBank\Domain\Item;
use ClubeDev\PagBank\Domain\Shipping;
use ClubeDev\PagBank\Domain\Address;
use ClubeDev\PagBank\Domain\Payment;
use ClubeDev\PagBank\Domain\Payment\CreditCard;
use ClubeDev\PagBank\Domain\Payment\Holder;

$response = $payment->createPayment(
    reference: "identificação-informada-por-você", // Geralmente utilizado algum id
    client: new Client(
        document: "12345678909", 
        name: "João", 
        email: "email@email.com",
        phones: [ // Opcional
            new Phone(
                country: 55,
                area: 21,
                number: 999121314
            ),
            new Phone(
                country: 55, 
                area: 21, 
                number: 999151617
            ),
        ]
    ),
    items: [ // Opcional
        new Item(
            name: 'Item X',
            quantity: 1,
            unit_amount: 21.36
        )
    ],
    shipping: new Shipping( // Opcional
        address: new Address(
            street: 'Rua 01',
            number: '01', // Opcional
            complement: 'Q01 L01', // Opcional
            locality: 'Bairro',
            city: 'Cidade',
            state_code: 'SP',
            postal_code: '01310930'
        )
    ),
    payment: new Payment(
        credit_card: new CreditCard(
            amount: 21.36,
            description: 'Descrição do pagamento',
            soft_descriptor: 'NOMEFATURACLIENTE',
            card_token: 'TOKEN_GERADO_PELO_SDK_JS', // Utilize ClubeDev PagBank Card Encrypt
            holder: new Holder(
                document: '123.456.789-09',
                name: 'Wanderson Teste',
            )
        )
    ),
    webhook_url: 'https://exemplo.com/webhook' // Opcional
);
```

### Retorno esperado

Método | Tipo Retorno | Descrição
--- | --- | ---
$response->raw() | Array | Este método retorna os dados brutos
$response->orderId() | String | ID do pedido PagBank
$response->reference() | Mixed | Referência enviada por você ao PagBank
$response->createdAt() | String | Data de criação do pagamento, formato: Y-m-d H:i:s
$response->status() | String | Status do pagamento
$response->shipping() | Shipping* ou null | Caso tenha enviado dados de entrega, será retornado aqui
$response->items() | Array de Item* | Caso tenha enviado items, será retornado aqui
$response->client() | Client* | Dados do cliente que irá realizar o pagamento
$response->payment() | Payment* | Dados do pagamento

\* Shipping, Item, Client e Payment são domain para tratamento de dados, no fim da documentação terá os detalhes de cada um 

### Exemplo de uso do retorno
```php
$amount = $response->payment()?->credit_card?->amount;
$description = $response->payment()?->credit_card?->description;
$softDescriptor = $response->payment()?->credit_card?->soft_descriptor;
$chargeId = $response->payment()?->credit_card?->charge_id;
$reference = $response->payment()?->credit_card?->reference;
$authorizationCode = $response->payment()?->credit_card?->authorization_code;
$nsu = $response->payment()?->credit_card?->nsu;
$brand = $response->payment()?->credit_card?->brand;
$cardNumber = $response->payment()?->credit_card?->card_number;
$expiration = $response->payment()?->credit_card?->expiration;
$holderName = $response->payment()?->credit_card?->holder?->name;
$holderDocument = $response->payment()?->credit_card?->holder?->document;
```

---

## Criar pagamento Boleto

```php
use ClubeDev\PagBank\Domain\Client;
use ClubeDev\PagBank\Domain\Phone;
use ClubeDev\PagBank\Domain\Item;
use ClubeDev\PagBank\Domain\Shipping;
use ClubeDev\PagBank\Domain\Address;
use ClubeDev\PagBank\Domain\Payment;
use ClubeDev\PagBank\Domain\Payment\Title;
use ClubeDev\PagBank\Domain\Payment\Holder;

$response = $payment->createPayment(
    reference: "identificação-informada-por-você", // Geralmente utilizado algum id
    client: new Client(
        document: "12345678909", 
        name: "João", 
        email: "email@email.com",
        phones: [ // Opcional
            new Phone(
                country: 55,
                area: 21,
                number: 999121314
            ),
            new Phone(
                country: 55, 
                area: 21, 
                number: 999151617
            ),
        ]
    ),
    items: [ // Opcional
        new Item(
            name: 'Item X',
            quantity: 1,
            unit_amount: 21.36
        )
    ],
    shipping: new Shipping( // Opcional
        address: new Address(
            street: 'Rua 01',
            number: '01', // Opcional
            complement: 'Q01 L01', // Opcional
            locality: 'Bairro',
            city: 'Cidade',
            state_code: 'SP',
            postal_code: '01310930'
        )
    ),
    payment: new Payment(
        title: new Title(
            description: 'Descrição do boleto',
            amount: 14.98,
            due_date: '2025-11-20',
            holder: new Holder(
                document: '12345678909',
                name: 'Wanderson Teste',
                email: 'wanderson@teste.com',
                address: new Address(
                    street: 'Rua 01',
                    number: '01', // Opcional
                    locality: 'Bairro',
                    city: 'Cidade',
                    state: 'São Paulo',
                    state_code: 'SP',
                    postal_code: '01310930'
                )
            )
        )
    ),
    webhook_url: 'https://exemplo.com/webhook' // Opcional
);
```

### Retorno esperado

Método | Tipo Retorno | Descrição
--- | --- | ---
$response->raw() | Array | Este método retorna os dados brutos
$response->orderId() | String | ID do pedido PagBank
$response->reference() | Mixed | Referência enviada por você ao PagBank
$response->createdAt() | String | Data de criação do pagamento, formato: Y-m-d H:i:s
$response->status() | String | Status do pagamento
$response->shipping() | Shipping* ou null | Caso tenha enviado dados de entrega, será retornado aqui
$response->items() | Array de Item* | Caso tenha enviado items, será retornado aqui
$response->client() | Client* | Dados do cliente que irá realizar o pagamento
$response->payment() | Payment* | Dados do pagamento

\* Shipping, Item, Client e Payment são domain para tratamento de dados, no fim da documentação terá os detalhes de cada um 

### Exemplo de uso do retorno
```php
$amount = $response->payment()?->title?->amount;
$description = $response->payment()?->title?->description;
$dueDate = $response->payment()?->title?->due_date;
$chargeId = $response->payment()?->title?->charge_id;
$barCode = $response->payment()?->title?->bar_code;
$urlPdf = $response->payment()?->title?->url;
$holderName = $response->payment()?->title?->holder?->name;
$holderDocument = $response->payment()?->title?->holder?->document;
$holderEmail = $response->payment()?->title?->holder?->email;
$holderAddressStreet = $response->payment()?->title?->holder?->address?->street;
$holderAddressNumber = $response->payment()?->title?->holder?->address?->number;
$holderAddressLocality = $response->payment()?->title?->holder?->address?->locality;
$holderAddressCity = $response->payment()?->title?->holder?->address?->city;
$holderAddressState = $response->payment()?->title?->holder?->address?->state;
$holderAddressStateCode = $response->payment()?->title?->holder?->address?->state_code;
$holderAddressPostal_code = $response->payment()?->title?->holder?->address?->postal_code;
```

---

## Consultar pagamento

Para consultar o pagamento será necessário informar o **ORDER_ID** que é encontrado na criação do pagamento **$response->orderId()**

```php
$response = $payment->getPayment('ORDER_ID');
```

### Retorno esperado

Método | Tipo Retorno | Descrição
--- | --- | ---
$response->raw() | Array | Este método retorna os dados brutos
$response->orderId() | String | ID do pedido PagBank
$response->reference() | Mixed | Referência enviada por você ao PagBank
$response->createdAt() | String | Data de criação do pagamento, formato: Y-m-d H:i:s
$response->status() | String | Status do pagamento
$response->shipping() | Shipping* ou null | Caso tenha enviado dados de entrega, será retornado aqui
$response->items() | Array de Item* | Caso tenha enviado items, será retornado aqui
$response->client() | Client* | Dados do cliente que irá realizar o pagamento
$response->payment() | Payment* | Dados do pagamento

\* Shipping, Item, Client e Payment são domain para tratamento de dados, no fim da documentação terá os detalhes de cada um 

### Exemplo de uso do retorno pix
```php
$amount = $response->payment()?->pix?->amount;
$expirationDate = $response->payment()?->pix?->expiration_date;
$chargeId = $response->payment()?->pix?->charge_id;
$qrcode = $response->payment()?->pix?->qrcode;
$qrcodeImage = $response->payment()?->pix?->qrcode_image;
```

### Exemplo de uso do retorno cartão de crédito
```php
$amount = $response->payment()?->credit_card?->amount;
$description = $response->payment()?->credit_card?->description;
$softDescriptor = $response->payment()?->credit_card?->soft_descriptor;
$chargeId = $response->payment()?->credit_card?->charge_id;
$reference = $response->payment()?->credit_card?->reference;
$authorizationCode = $response->payment()?->credit_card?->authorization_code;
$nsu = $response->payment()?->credit_card?->nsu;
$brand = $response->payment()?->credit_card?->brand;
$cardNumber = $response->payment()?->credit_card?->card_number;
$expiration = $response->payment()?->credit_card?->expiration;
$holderName = $response->payment()?->credit_card?->holder?->name;
$holderDocument = $response->payment()?->credit_card?->holder?->document;
```

### Exemplo de uso do retorno boleto
```php
$amount = $response->payment()?->title?->amount;
$description = $response->payment()?->title?->description;
$dueDate = $response->payment()?->title?->due_date;
$chargeId = $response->payment()?->title?->charge_id;
$barCode = $response->payment()?->title?->bar_code;
$urlPdf = $response->payment()?->title?->url;
$holderName = $response->payment()?->title?->holder?->name;
$holderDocument = $response->payment()?->title?->holder?->document;
$holderEmail = $response->payment()?->title?->holder?->email;
$holderAddressStreet = $response->payment()?->title?->holder?->address?->street;
$holderAddressNumber = $response->payment()?->title?->holder?->address?->number;
$holderAddressLocality = $response->payment()?->title?->holder?->address?->locality;
$holderAddressCity = $response->payment()?->title?->holder?->address?->city;
$holderAddressState = $response->payment()?->title?->holder?->address?->state;
$holderAddressStateCode = $response->payment()?->title?->holder?->address?->state_code;
$holderAddressPostal_code = $response->payment()?->title?->holder?->address?->postal_code;
```

---

# Cancelamentos

O mesmo método é utilizado tanto para cancelamento parcial quanto completo.
Este método espera o **CHARGE_ID** que é encontrado na criação ou busca do pagamento:
- PIX: **$response->payment()?->pix?->charge_id**
- Cartão de crédito: **$response->payment()?->credit_card?->charge_id**
- Boleto: **$response->payment()?->title?->charge_id**

```php
$response = $payment->cancelPayment(
    charge_id: 'CHARGE_ID', 
    amount: 16.99
);
```

### Cancelamento parcial
Se o valor enviado for **menor que o total pago**, o PagBank realizará um cancelamento parcial.

### Cancelamento total
Se o valor for **igual ao valor total pago**, será realizado um cancelamento completo.

### Retorno esperado

Método | Tipo Retorno | Descrição
--- | --- | ---
$response->raw() | Array | Este método retorna os dados brutos
$response->canceled() | Boolean | Informa se sua requisição foi bem executada
$response->id() | String | ID do pedido PagBank
$response->fullRefunded() | Boolean | Informa se o pagamento foi completamente estornado
$response->paid() | Float | Valor pago pelo cliente
$response->refunded() | Float | Valor estornado ao cliente

---

# Processamento Webhook
Para facilitar, desenvolvemos um método que irá processar o webhook para você.

```php
$body = file_get_contents('php://input');
$paymentWebhook = $payment->webhook(json_decode($body, true));
```

### Retorno esperado

Método | Tipo Retorno | Descrição
--- | --- | ---
$response->raw() | Array | Este método retorna os dados brutos
$response->orderId() | String | ID do pedido PagBank
$response->reference() | Mixed | Referência informada por você na criação do pagamento
$response->createdAt() | String | Data de criação do pagamento, formato **Y-m-d H:i:s**
$response->status() | String | Status do pagamento
$response->paid() | Boolean | Informa se o pagamento foi concluído
$response->canceled() | Boolean | Informa se o pagamento foi cancelado
$response->waiting() | Boolean | Informa se o pagamento está pendente
$response->inAnalysis() | Boolean | Informa se o pagamento está em análise
$response->declined() | Boolean | Informa se o pagamento foi recusado
$response->authorized() | Boolean | Informa se o pagamento foi autorizado
$response->shipping() | Shipping* ou null | Caso tenha enviado dados de entrega, será retornado aqui
$response->items() | Array de Item* | Caso tenha enviado items, será retornado aqui
$response->client() | Client* | Dados do cliente que irá realizar o pagamento
$response->payment() | Payment* | Dados do pagamento

\* Shipping, Item, Client e Payment são domain para tratamento de dados, no fim da documentação terá os detalhes de cada um 

# Domínios (Domains)

A biblioteca usa objetos para garantir que os dados enviados ao backend estejam no formato correto e também uma forma de utilização/formatação dos dados recebidos do backend.

Abaixo, um resumo simples:

### Phone
```php
use ClubeDev\PagBank\Domain\Phone;

new Phone(
    country: 55, 
    area: 21, 
    number: 999999999
);
```

### Client
```php
use ClubeDev\PagBank\Domain\Client;

new Client(
    document: "12345678900",
    name: "Fulano Teste",
    email: "teste@email.com",
    phones: [ 
        new Phone(country: 55, area: 21, number: 999999999),
        new Phone(country: 55, area: 21, number: 999999999),
    ]
);
```

### Item
```php
use ClubeDev\PagBank\Domain\Item;

new Item(
    name: 'Item teste',
    quantity: 1,
    unit_amount: 12.99
);
```

### Address
```php
use ClubeDev\PagBank\Domain\Address;

new Address(
    street: 'Rua 01',
    number: '01',
    complement: 'Q01 L01',
    locality: 'Bairro',
    city: 'Cidade',
    state: 'São Paulo',
    state_code: 'SP',
    postal_code: '01310930'
);
```

### Shipping
```php
use ClubeDev\PagBank\Domain\Shipping;

new Shipping(
    address: new Address(...),
);
```

### Holder
```php
use ClubeDev\PagBank\Domain\Payment\Holder;

new Holder(
    document: '123.456.789-09',
    name: 'Fulano Teste',
    email: 'teste@teste.com',
    address: new Address(...)
)
```

### CreditCard
```php
use ClubeDev\PagBank\Domain\Payment\CreditCard;

new CreditCard(
    amount: 11.50,
    description: 'Descrição do pagamento',
    soft_descriptor: 'NOMEFATURA', // Descrição que aparecerá na fatura do cartão
    holder: new Holder(...),
    card_token: 'TOKEN_GERADO_PELO_SDK_JS',
    installments: 1,
    charge_id: null, // Preenchido automaticamente pelo backend
    reference: null, // Preenchido automaticamente pelo backend
    authorization_code: null, // Preenchido automaticamente pelo backend
    nsu: null, // Preenchido automaticamente pelo backend
    brand: null, // Preenchido automaticamente pelo backend
    card_number: null, // Preenchido automaticamente pelo backend
    expiration: null, // Preenchido automaticamente pelo backend
)
```

### Title
```php
use ClubeDev\PagBank\Domain\Payment\Title;

new Title(
    amount: 11.50,
    description: 'Descrição do boleto para controle interno',
    due_date: '2025-12-01',
    holder: new Holder(...),
    instruction_line_1: 'Não receber após vencimento',
    instruction_line_2: 'Pagável apenas na loterica',
    charge_id: null, // Preenchido automaticamente pelo backend
    bar_code: null, // Preenchido automaticamente pelo backend
    url: null, // Preenchido automaticamente pelo backend
)
```

### Pix
```php
use ClubeDev\PagBank\Domain\Payment\Pix;

new Pix(
    amount: 11.50,
    expiration_date: '2025-12-01 12:30:00',
    charge_id: null, // Preenchido automaticamente pelo backend
    qrcode: null, // Preenchido automaticamente pelo backend
    qrcode_image: null, // Preenchido automaticamente pelo backend
)
```

### Payment
```php
use ClubeDev\PagBank\Domain\Payment;

new Payment(
    pix: new Pix(...),
    title: new Title(...),
    credit_card: new CreditCard(...),
    paid_at: null, // Preenchido automaticamente pelo backend
    status: null, // Preenchido automaticamente pelo backend
    full_refunded: null, // Preenchido automaticamente pelo backend
    paid: null, // Preenchido automaticamente pelo backend
    refunded: null, // Preenchido automaticamente pelo backend
)
```

---

# Tratamento de Exceções

Todas as operações podem lançar exceções:

- `ClubedevException` – Erro lançado pelo ClubeDev
- `PagBankException` – Erro lançado pelo PagBank

### Exemplo de uso seguro:

```php
use ClubeDev\PagBank\Exceptions\ClubedevException;
use ClubeDev\PagBank\Exceptions\PagBankException;

try {
    $response = $payment->getPayment('ORDER_ID');
} catch(ClubedevException $e) {
    $exception = json_decode($e->getMessage());
    echo $exception?->error ?? $exception?->message ?? $e->getMessage();
} catch(PagBankException $e) {
    $exception = json_decode($e->getMessage());
    echo $exception?->error ?? $exception?->message ?? $e->getMessage();
} catch (\Throwable $th) {
    echo $th->getMessage();
}
```



# Suporte

- **Site ClubeDev:** https://clubedev.com.br  
- Suporte técnico via painel do cliente  
- Exemplos e atualizações no repositório oficial

---

⌛ *Desenvolvido para ser simples, direto e produtivo.*  
