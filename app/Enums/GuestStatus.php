<?php

namespace App\Enums;


enum GuestStatus: string {

use EnumToArray;

    case PRESENT = "Presente";
    case ABSENT = "Ausente";
    case PENDENT = "Pendente";
    case BLOCKED = "Bloqueado";
}