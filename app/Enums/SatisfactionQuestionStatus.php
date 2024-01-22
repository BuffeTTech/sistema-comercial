<?php

namespace App\Enums;


enum FoodStatus: string {

use EnumToArray;

    case ACTIVE = "Ativo";
    case UNACTIVE = "Inativo";
    case PENDENT = "Pendente";
}