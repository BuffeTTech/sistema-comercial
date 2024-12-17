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
    case PAID = "Pago";
    case LATE = "Atrasado";
    case PAY_LATER = "Pagar depois";
    case VISIT_FIRST = "Visitar Antes";
}