<?php

return [
    'beneficiary'  => env('BANK_BENEFICIARY', 'Copyus S.L.'),
    'iban'         => env('BANK_IBAN', 'ES12 3456 7890 1234 5678 9012'),
    'bic'          => env('BANK_BIC', 'CAIXESBBXXX'),
    'bank_name'    => env('BANK_NAME', 'CaixaBank'),
    'reference_note' => 'Please include the order number as payment reference',
];
