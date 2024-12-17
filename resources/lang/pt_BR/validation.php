<?php

return [
    'required' => 'O campo :attribute é obrigatório.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'string' => 'O campo :attribute deve ser uma string.',
    'accepted' => 'Você deve aceitar :attribute.',
    'min' => [
        'numeric' => 'O campo :attribute deve ser maior ou igual a :min.',
    ],
    'regex' => 'O campo :attribute deve seguir o formato válido.',
    'custom' => [
        'min_days_booking' => [
            'required' => 'O campo Dias mínimos para reserva é obrigatório.',
            'integer' => 'O campo Dias mínimos para reserva deve ser um número inteiro.',
            'min' => 'O campo Dias mínimos para reserva deve ser maior ou igual a 0.',
        ],
        'max_days_unavaiable_booking' => [
            'required' => 'O campo Dias máximos de indisponibilidade para reserva é obrigatório.',
            'integer' => 'O campo Dias máximos de indisponibilidade para reserva deve ser um número inteiro.',
            'min' => 'O campo Dias máximos de indisponibilidade para reserva deve ser maior ou igual a 0.',
        ],
        // Continue para os outros campos...
    ],
    'attributes' => [
        'min_days_booking' => 'Dias mínimos para reserva',
        'max_days_unavaiable_booking' => 'Dias máximos de indisponibilidade para reserva',
        'buffet_instagram.required' => 'O campo Instagram é obrigatório.',
        'buffet_instagram.url' => 'O campo Instagram do buffet deve ser uma URL válida do Instagram.',
        'buffet_linkedin.required' => 'O campo LinkedIn é obrigatório.',
        'buffet_linkedin.url' => 'O campo LinkedIn do buffet deve ser uma URL válida do LinkedIn.',
        'buffet_facebook.required' => 'O campo Facebook é obrigatório.',
        'buffet_facebook.url' => 'O campo Facebook do buffet deve ser uma URL válida do Facebook.',
        'buffet_whatsapp.required' => 'O campo WhatsApp é obrigatório.',
        'buffet_whatsapp.regex' => 'O campo WhatsApp do buffet deve seguir o formato válido: https://wa.me/{número}.',
        'external_decoration' => 'decoração externa',
        'charge_by_schedule' => 'cobrança por horário',
        'allow_post_payment' => 'pagamento postergado',
        'children_affect_pricing' => 'opção de preços para crianças',
        'children_price_adjustment' => 'ajuste de preço para crianças',
        'child_age_limit' => 'limite de idade para crianças',
    ],
];
