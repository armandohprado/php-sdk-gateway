# Payment

## Usage

```php
require_once __DIR__ . '/../vendor/autoload.php';
use GatewaySdk\Payment;
use GatewaySdk\GatewayClient;

$guzzle = new GatewayClient;
$payment = new Payment($guzzle);
$payment->setToken("<YOUR TOKEN>");
```

### Create and Authorize Payment
```php
$payment_arr = [
    "externalId" => "23524234322523-32523",
    "capture" => true,
    "amount" => 100,
    "interest" => 0,
    "creditCard" => [
        "installments" => 1,
        "number" => "4111.1111.1111.1111",
        "holder" => "John Doe",
        "expiration" => "12/2020",
        "cvv" => "123",
        "brand" => "visa"
    ],
    "customer" => [
        "externalId" => "1234",
        "name" => "John Doe",
        "document" => "112.452.662-12",
        "address" => [
            "street" => "Rua Joaquim Floriano",
            "number" => "820",
            "complement" => "20 andar",
            "zipcode" => "04534-003",
            "city" => "São Paulo",
            "state" => "SP",
            "country" => "BR"
        ]
    ],
    "items" => [
        [
            "externalId" => "123",
            "type" => "ticket",
            "name" => "My VIP ticket",
            "unitPrice" => 100,
            "quantity" => 1
        ]
    ]
];

$response = $payment->authorize($payment_arr);
var_dump($response->isSuccess()); // true
var_dump($response->getData());
/*
[
    "type" => "creditCard",
    "updatedAt" => "2017-10-25T12:46:58.931705",
    "statusHistory" => [
        [
            "status" => "started",
            "createdAt" => "2017-10-25T12:46:00.360891"
        ],
        [
            "status" => "paid",
            "createdAt" => "2017-10-25T12:47:57.769956"
        ]
    ],
    "status" => "paid",
    "items" => [
        [
            "unitPrice" => 100,
            "type" => "ticket",
            "quantity" => 1,
            "name" => "My VIP ticket",
            "sku" => "123"
        ]
    ],
    "id" => "4906c13a-91b4-4c73-b970-b1b4bde11568",
    "externalId" => "23524234322523-32523",
    "details" => null,
    "customer" => [
        "name" => "John Doe",
        "externalId" => "1234",
        "document" => "112.452.662-12",
        "address" => [
            "street" => "Rua Joaquim Floriano",
            "number" => "820",
            "complement" => "20 andar",
            "zipcode" => "04534-003",
            "city" => "São Paulo",
            "state" => "SP",
            "country" => "BR"
        ]
    ],
    "creditCard" => [
        "token" => "1f232636-6fad-43f8-af84-3c1145c52a5c",
        "masked" => "411111****1111",
        "id" => "08aa8524-ee4d-4912-b779-0c6550f98345",
        "expiration" => "2017-10-24T21:51:18.824452",
        "brand" => "Visa"
    ],
    "createdAt" => "2017-10-25T12:46:57.733966",
    "companyId" => 1,
    "bankingBillet" => null,
    "applicationId" => "1008769",
    "amount" => 100,
    "currency" => "BRL",
    "acquirer" => "stone"
]
*/
```

### Get Payment by id
```php
$paymentId = "ABC-123";
$response = $payment->get($paymentId);
```

### Capture Payment
```php
$paymentId = "ABC-123";
$response = $payment->capture($paymentId);
```

### Refund Payment
```php
$paymentId = "ABC-123";
$response = $payment->refund($paymentId);
```

## Errors
### 4xx errors
#### Field validation error
```php

// payment without amount
$payment_arr = [
    "externalId" => "23524234322523-32523",
    "capture" => true,
    "amount" => null,
    "interest" => 0,
    "creditCard" => [
        "installments" => 1,
        "number" => "4111.1111.1111.1111",
        "holder" => "John Doe",
        "expiration" => "12/2020",
        "cvv" => "123",
        "brand" => "visa"
    ],
    "customer" => [
        "externalId" => "1234",
        "name" => "John Doe",
        "document" => "112.452.662-12",
        "address" => [
            "street" => "Rua Joaquim Floriano",
            "number" => "820",
            "complement" => "20 andar",
            "zipcode" => "04534-003",
            "city" => "São Paulo",
            "state" => "SP",
            "country" => "BR"
        ]
    ],
    "items" => [
        [
            "externalId" => "123",
            "type" => "ticket",
            "name" => "My VIP ticket",
            "unitPrice" => 100,
            "quantity" => 1
        ]
    ]
];

$response = $payment->authorize($payment_arr);
var_dump($response->isSuccess()); // false
var_dump($response->getError());
/*
[
    "status" => 400,
    "info" => [
        "amount" => [
            "can't be blank"
        ]
    ]
]

*/
```

#### Request validation error
```php
require_once __DIR__ . '/../vendor/autoload.php';
use GatewaySdk\Payment;
use GatewaySdk\GatewayClient;

$guzzle = new GatewayClient;
$payment = new Payment($guzzle);

// execute the request without setting the application token
$paymentId = "ABC-123";
try {
    $response = $payment->get($paymentId);
}
catch (ApiServerException $e) {
    var_dump($e->getMesage());
}
// The Gateway API returned an error code 401. Details: Client error: `GET payment` resulted in a `401 Unauthorized` response: {"error": {"status": 401, "error": "Missing Authorization header"}}


```
### 5xx errors
```php
require_once __DIR__ . '/../vendor/autoload.php';
use GatewaySdk\Payment;
use GatewaySdk\GatewayClient;
use GatewaySdk\Exception\ApiServerException;

$guzzle = new GatewayClient;
$payment = new Payment($guzzle);
$payment->setToken("<YOUR TOKEN>");

$paymentId = "ABC-123";
try {
    $response = $payment->get($paymentId);
}
catch (ApiServerException $e) {
    var_dump($e->getMesage());
}
// The Gateway API returned an error code 500. Details: Server error: `GET payment` resulted in a `500 Internal Server Error` response
```
