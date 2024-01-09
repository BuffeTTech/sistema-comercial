<?php

if (!function_exists('format_slug')) {
    function format_slug(string $slug): string
    {
        $slug = strtolower($slug);
        return str_replace(' ', '-', $slug);;
    }
}

if (!function_exists('sanitize_string')) {
    function sanitize_string(string $string): string
    {
        // Converte para minúsculas
        $string = strtolower($string);

        // Substitui espaços por hífens
        $string = str_replace(' ', '-', $string);

        // Mapeia caracteres especiais para suas versões tradicionais
        $caracteresEspeciais = [
            'á' => 'a',
            'ã' => 'a',
            'â' => 'a',
            'à' => 'a',
            'é' => 'e',
            'ê' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ú' => 'u',
            'ü' => 'u',
            'ç' => 'c',
            // Adicione mais caracteres conforme necessário
        ];

        // Substitui caracteres especiais
        $string = strtr($string, $caracteresEspeciais);

        return $string;
    }
}