@props(['status'])

@if ($status)
    @php
        switch($status) {
            case 'PENDENT':
                $class = "badge badge-md bg-warning";
                $name = \App\Enums\BookingStatus::getEnumByName($status)->value;
                break;
            break;

            case 'APPROVED':
                $class = "badge badge-md bg-success";
                $name = \App\Enums\BookingStatus::getEnumByName($status)->value;
                break;

            case 'REJECTED':
            case 'CANCELED':
                $class = "badge badge-md bg-danger";
                $name = \App\Enums\BookingStatus::getEnumByName($status)->value;
                break;

            case 'FINISHED':
            case 'CLOSED':
                $class = "badge badge-md bg-info";
                $name = \App\Enums\BookingStatus::getEnumByName($status)->value;
                break;

            default:
                $class = "badge badge-md bg-secondary";
                $name = \App\Enums\BookingStatus::getEnumByName($status)->value;
                break;
        }
    @endphp

    <span class="{{$class}}">{{ $name }}</span>
@endif