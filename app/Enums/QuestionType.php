<?php

namespace App\Enums;


enum QuestionType: string {

    use EnumToArray;

    case M = "Múltipla Escolha";
    case D = "Dissertativa";

	//função que irá retornar o value de algum status
    public static function fromValue(string $name): string {
        foreach (self::cases() as $status) {
            if($name === $status->name) {
                return $status->value;
            }
        }

        throw new \ValueError("$status is not valid");
    }
}