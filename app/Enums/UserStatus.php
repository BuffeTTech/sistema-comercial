<?php

namespace App\Enums;


enum UserStatus: string {

use EnumToArray;

    case ACTIVE = "Ativo";
    case UNACTIVE = "Inativo";
    case PENDENT = "Pendente";
}