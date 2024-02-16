@props(['status'])

@if ($status)
    @php
        $class = "";
        switch($status) {
            case 'PRESENT':
                $class = "badge badge-md bg-light ";
                $name = \App\Enums\GuestStatus::getEnumByName($status)->value;
                break;
            case 'ABSENT':
                $class = "badge badge-md bg-warning";
                $name = \App\Enums\GuestStatus::getEnumByName($status)->value;
            break;
            case 'BLOCKED':
                $class = "badge badge-md bg-danger";
                $name = \App\Enums\GuestStatus::getEnumByName($status)->value;
            break;
            case 'PENDENT':
                $class = "badge badge-md bg-info";
                $name = \App\Enums\GuestStatus::getEnumByName($status)->value;
                break;
            case 'CONFIRMED':
                $class = "badge badge-md bg-success";
                $name = \App\Enums\GuestStatus::getEnumByName($status)->value;
                break;
            case 'EXTRA':
                $class = "badge badge-md bg-dark";
                $name = \App\Enums\GuestStatus::getEnumByName($status)->value;
                break;
            default:
                $class = "badge badge-md bg-secondary";
                $name = "Desconhecido";
                break;
        }
    @endphp

    <span class="{{$class}}">{{ \App\Enums\GuestStatus::getEnumByName($status) }}</span>
@endif