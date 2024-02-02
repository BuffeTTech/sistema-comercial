<?php

namespace App\Enums;


enum RecommendationStatus: string {

use EnumToArray;

    case ACTIVE = "Ativo";
    case UNACTIVE = "Inativo";
    case PENDENT = "Pendente";
}