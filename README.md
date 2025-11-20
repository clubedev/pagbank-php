# PHP PagBank SDK — Integração com PagBank + ClubeDev

Este SDK facilita pagamentos com PagBank utilizando a ponte oficial da **ClubeDev**, permitindo criar cobranças, consultar transações e realizar cancelamentos totais ou parciais de maneira simples e padronizada.

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
  - [Consultar pagamento](#consultar-pagamento)
- [Cancelamentos](#cancelamentos)
  - [Cancelamento parcial](#cancelamento-parcial)
  - [Cancelamento total](#cancelamento-total)
- [Domínios (Domains)](#domínios-domains)
- [Tratamento de Exceções](#tratamento-de-exceções)
- [Exemplos completos](#exemplos-completos)
- [Suporte](#suporte)

---

# Instalação

```bash
composer require clubedev/php-pagbank
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

```json
{
  "transaction_id": "abc123",
  "status": "waiting_payment",
  "qr_code": "...",
  "expires_at": "2025-02-10T12:00:00-03:00"
}
```

---

## Criar pagamento Cartão

```php
$response = $payment->creditCard()->create(
    amount: 120.50,
    description: 'Compra camiseta',
    customer: [
        'name' => 'Maria Alves',
        'email' => 'maria@email.com',
        'cpf' => '98765432100'
    ],
    card: [
        'number' => '4111111111111111',
        'exp_month' => '12',
        'exp_year' => '2029',
        'cvv' => '123'
    ]
);
```

---

## Consultar pagamento

```php
$response = $payment->transactions()->get('transaction_id_aqui');
```

---

# Cancelamentos

O mesmo método é utilizado tanto para cancelamento parcial quanto completo.

```php
$payment->transactions()->cancel(
    transactionId: 'abc123',
    amount: 20.00 // opcional
);
```

## Cancelamento parcial
Se o valor enviado for **menor que o total pago**, o PagBank realizará um cancelamento parcial.

## Cancelamento total
Se o valor for **igual ao valor total pago**, será realizado um cancelamento completo.

---

# Domínios (Domains)

A biblioteca é organizada em módulos independentes:

| Módulo | Descrição |
|-------|-----------|
| `$payment->pix()` | Operações Pix |
| `$payment->creditCard()` | Cobranças via cartão |
| `$payment->transactions()` | Consultar e cancelar transações |
| `$payment->boleto()` *(se aplicável)* | Emissão de boletos |
| `$payment->utils()` | Métodos auxiliares |

---

# Tratamento de Exceções

Todas as operações podem lançar exceções:

- `PagBankAuthenticationException` – token inválido ou ausente  
- `PagBankRequestException` – erro ao enviar requisição  
- `PagBankValidationException` – dados inválidos (CPF, cartão, valor, etc.)  
- `PagBankServerException` – PagBank fora do ar  
- `ClubeDevTokenException` – token ClubeDev inválido  

### Exemplo de uso seguro:

```php
try {
    $response = $payment->pix()->create(100, 'Pedido XPTO', $customer);
} catch (\Throwable $e) {
    echo "Erro ao processar pagamento: " . $e->getMessage();
}
```

---

# Exemplos completos

## Fluxo completo Pix

```php
$payment = new PagBank('token_pagbank', 'token_clubedev', true);

$customer = [
    'name' => 'Pedro Moreira',
    'email' => 'pedro@email.com',
    'cpf' => '11122233344'
];

$pix = $payment->pix()->create(
    amount: 89.90,
    description: 'Assinatura Premium',
    customer: $customer
);

print_r($pix);
```

---

## Fluxo completo com cancelamento

```php
$payment = new PagBank('token_pagbank', 'token_clubedev', true);

$transaction = $payment->transactions()->get('abc123');

if ($transaction['status'] === 'paid') {
    // cancelar parcialmente
    $payment->transactions()->cancel('abc123', 10.00);
}
```

---

# Suporte

- **Site ClubeDev:** https://clubedev.com.br  
- Suporte técnico via painel do cliente  
- Exemplos e atualizações no repositório oficial

---

⌛ *Desenvolvido para ser simples, direto e produtivo.*  
