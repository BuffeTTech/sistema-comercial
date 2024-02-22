@props(['status'])

@php
    $class = "";
    switch($status) {
        case true:
            $class = "badge badge-md bg-success";
            $name = "Ativado";
            break;
        case false:
            $class = "badge badge-md bg-danger";
            $name = "Desativado";
            break;
        break;
        default:
            $class = "badge badge-md bg-secondary";
            $name = "Desconhecido";
            break;
    }
@endphp
<span class="{{$class}}">{{ $name }}</span>