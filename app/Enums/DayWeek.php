<?php

namespace App\Enums;


enum DayWeek: string
{

    use EnumToArray;

    case SUNDAY = 'Domingo';
    case MONDAY = 'Segunda-feira';
    case TUESDAY = 'Terça-feira';
    case WEDNESDAY = 'Quarta-feira';
    case THURSDAY = 'Quinta-feira';
    case FRIDAY = 'Sexta-feira';
    case SATURDAY = 'Sábado';

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

    public static function isWeekend(self $dayOfWeek): bool {
        $weekendDays = [self::FRIDAY, self::SATURDAY, self::SUNDAY];
        return in_array($dayOfWeek, $weekendDays, true);
    }

}
