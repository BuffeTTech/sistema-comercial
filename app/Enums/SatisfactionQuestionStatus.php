<?php

namespace App\Enums;


enum SatisfactionQuestionStatus: string {

use EnumToArray;

    case ACTIVE = "Ativo";
    case UNACTIVE = "Inativo";
    case PENDENT = "Pendente";
}