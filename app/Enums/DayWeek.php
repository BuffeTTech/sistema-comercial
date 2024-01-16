<?php

namespace App\Enums;


enum DayWeek: string {

use EnumToArray;

    case SEGUNDA = "Segunda";
    case TERÇA = "Terça";
    case QUARTA = "Quarta";
    case QUINTA = "Quinta";
    case SEXTA = "Sexta";
    case SABADO = "Sabado";
    case DOMINGO = "Domingo";

}