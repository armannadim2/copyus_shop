<?php

return [
    'accepted'             => 'The :attribute must be accepted.',
    'array'                => 'The :attribute must be an array.',
    'file'                 => 'The :attribute must be a file.',
    'image'                => 'The :attribute must be an image.',
    'in'                   => 'The selected :attribute is invalid.',
    'integer'              => 'The :attribute must be an integer.',
    'max'                  => [
        'file'    => 'The :attribute may not be greater than :max kilobytes.',
        'numeric' => 'The :attribute may not be greater than :max.',
        'string'  => 'The :attribute may not be greater than :max characters.',
        'array'   => 'The :attribute may not have more than :max items.',
    ],
    'mimes'                => 'The :attribute must be a file of type: :values.',
    'mimetypes'            => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'file'    => 'The :attribute must be at least :min kilobytes.',
        'numeric' => 'The :attribute must be at least :min.',
        'string'  => 'The :attribute must be at least :min characters.',
        'array'   => 'The :attribute must have at least :min items.',
    ],
    'numeric'              => 'The :attribute must be a number.',
    'required'             => 'The :attribute field is required.',
    'required_if'          => 'The :attribute field is required when :other is :value.',
    'required_with'        => 'The :attribute field is required when :values is present.',
    'string'               => 'The :attribute must be a string.',
    'unique'               => 'The :attribute has already been taken.',
    'uploaded'             => 'The :attribute failed to upload.',
    'url'                  => 'The :attribute format is invalid.',

    'fiscal_identity' => 'The :attribute is not a valid Spanish NIF/DNI, NIE, CIF or European VAT number. '
        . 'Accepted formats: DNI (12345678Z), NIE (X1234567L), CIF (B12345678), EU VAT (DE123456789).',
];
