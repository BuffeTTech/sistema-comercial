<?php

namespace App\Enums;


enum BuffetStatus: string {

    use EnumToArray;

    case ACTIVE = "Ativo";
    case UNACTIVE = "Inativo";
    case PENDENT = "Pendente";
}