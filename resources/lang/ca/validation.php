<?php

return [
    'accepted'             => 'El camp :attribute ha de ser acceptat.',
    'array'                => 'El camp :attribute ha de ser un array.',
    'file'                 => ':attribute ha de ser un fitxer vàlid.',
    'image'                => ':attribute ha de ser una imatge.',
    'in'                   => 'El valor seleccionat per :attribute no és vàlid.',
    'integer'              => 'El camp :attribute ha de ser un nombre enter.',
    'max'                  => [
        'file'    => ':attribute no pot ser superior a :max kilobytes.',
        'numeric' => ':attribute no pot ser superior a :max.',
        'string'  => ':attribute no pot ser superior a :max caràcters.',
        'array'   => ':attribute no pot tenir més de :max elements.',
    ],
    'mimes'                => ':attribute ha de ser un fitxer de tipus: :values.',
    'mimetypes'            => ':attribute ha de ser un fitxer de tipus: :values.',
    'min'                  => [
        'file'    => ':attribute ha de ser com a mínim :min kilobytes.',
        'numeric' => ':attribute ha de ser com a mínim :min.',
        'string'  => ':attribute ha de tenir com a mínim :min caràcters.',
        'array'   => ':attribute ha de tenir com a mínim :min elements.',
    ],
    'nullable'             => '',
    'numeric'              => 'El camp :attribute ha de ser un nombre.',
    'required'             => 'El camp :attribute és obligatori.',
    'required_if'          => 'El camp :attribute és obligatori quan :other és :value.',
    'required_with'        => 'El camp :attribute és obligatori quan :values és present.',
    'sometimes'            => '',
    'string'               => 'El camp :attribute ha de ser una cadena de text.',
    'unique'               => ':attribute ja existeix.',
    'uploaded'             => 'El fitxer :attribute no s\'ha pogut pujar.',
    'url'                  => 'El format de :attribute no és vàlid.',

    'fiscal_identity' => 'El camp :attribute no és un NIF/DNI, NIE, CIF espanyol o VAT europeu vàlid. '
        . 'Formats acceptats: DNI (12345678Z), NIE (X1234567L), CIF (B12345678), VAT europeu (DE123456789).',
];
