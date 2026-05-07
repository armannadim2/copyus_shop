<?php

return [
    'accepted'             => 'El campo :attribute debe ser aceptado.',
    'array'                => 'El campo :attribute debe ser un array.',
    'file'                 => ':attribute debe ser un archivo válido.',
    'image'                => ':attribute debe ser una imagen.',
    'in'                   => 'El valor seleccionado para :attribute no es válido.',
    'integer'              => 'El campo :attribute debe ser un número entero.',
    'max'                  => [
        'file'    => ':attribute no puede ser mayor de :max kilobytes.',
        'numeric' => ':attribute no puede ser mayor de :max.',
        'string'  => ':attribute no puede ser mayor de :max caracteres.',
        'array'   => ':attribute no puede tener más de :max elementos.',
    ],
    'mimes'                => ':attribute debe ser un archivo de tipo: :values.',
    'mimetypes'            => ':attribute debe ser un archivo de tipo: :values.',
    'min'                  => [
        'file'    => ':attribute debe ser al menos :min kilobytes.',
        'numeric' => ':attribute debe ser al menos :min.',
        'string'  => ':attribute debe tener al menos :min caracteres.',
        'array'   => ':attribute debe tener al menos :min elementos.',
    ],
    'numeric'              => 'El campo :attribute debe ser un número.',
    'required'             => 'El campo :attribute es obligatorio.',
    'required_if'          => 'El campo :attribute es obligatorio cuando :other es :value.',
    'required_with'        => 'El campo :attribute es obligatorio cuando :values está presente.',
    'string'               => 'El campo :attribute debe ser una cadena de texto.',
    'unique'               => ':attribute ya existe.',
    'uploaded'             => 'El archivo :attribute no se pudo subir.',
    'url'                  => 'El formato de :attribute no es válido.',

    'fiscal_identity' => 'El campo :attribute no es un NIF/DNI, NIE, CIF español o VAT europeo válido. '
        . 'Formatos aceptados: DNI (12345678Z), NIE (X1234567L), CIF (B12345678), VAT europeo (DE123456789).',
];
