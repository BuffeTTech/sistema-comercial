<?php

namespace App\Enums;


enum SubscriptionStatus: string {

    use EnumToArray;

    case ACTIVE = "Ativo";
    case UNACTIVE = "Inativo";
    case PENDENT = "Pendente";
}