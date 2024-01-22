<?php

namespace App\Enums;


enum BuffetStatus: string {

    use EnumToArray;

    case ADMINISTRATIVE = "Admin";
    case COMERCIAL = "Comercial";
    case OPERATIONAL = "Operacional";

}