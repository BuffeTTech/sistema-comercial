<?php

namespace App\Enums;

enum DayTimePreference: string {

    use EnumToArray;
    
    case MORNING = "Manhã";
    case AFTERNOON = "Tarde";
    case NIGHT = "Noite";
}