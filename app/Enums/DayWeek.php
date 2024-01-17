<?php

namespace App\Enums;


enum DayWeek: string
{

    use EnumToArray;

    case SUNDAY = 'sunday';
    case MONDAY = 'monday';
    case TUESDAY = 'tuesday';
    case WEDNESDAY = 'wednesday';
    case THURSDAY = 'thursday';
    case FRIDAY = 'friday';
    case SATURDAY = 'saturday';

    public static function getNameInPortuguese($dayOfWeek)
    {
        $translations = [
            self::SUNDAY->name => 'Domingo',
            self::MONDAY->name => 'Segunda-feira',
            self::TUESDAY->name => 'Terça-feira',
            self::WEDNESDAY->name => 'Quarta-feira',
            self::THURSDAY->name => 'Quinta-feira',
            self::FRIDAY->name => 'Sexta-feira',
            self::SATURDAY->name => 'Sábado',
        ];

        return $translations[$dayOfWeek] ?? 'Desconhecido';
    }

}
