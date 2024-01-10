<?php

namespace App\Enums;


enum DecorationStatus: string {

    use EnumToArray;

    case ACTIVE = "Ativo";
    case UNACTIVE = "Inativo";
    case PENDENT = "Pendente";
}