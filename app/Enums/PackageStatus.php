<?php

namespace App\Enums;


enum PackageStatus: string {

use EnumToArray;

    case ACTIVE = "Ativo";
    case UNACTIVE = "Inativo";
    case PENDENT = "Pendente";
}