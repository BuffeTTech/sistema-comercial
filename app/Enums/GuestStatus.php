<?php

namespace App\Enums;


enum GuestStatus: string {

use EnumToArray;

    case CONFIRMED = "Confirmado";
    case PRESENT = "Presente";
    case ABSENT = "Ausente";
    case PENDENT = "Pendente";
    case BLOCKED = "Bloqueado";
    case EXTRA = 'Extra';
}