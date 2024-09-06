<?php

namespace App\Enums;

enum BookingStatus: string {

    use EnumToArray;
    
    case REJECTED = "Negado";
    case PENDENT = "Pendente";
    case APPROVED = "Aprovado";
    case CANCELED = "Cancelado";
    case FINISHED = "Finalizado";
    case CLOSED = "Encerrado";
}