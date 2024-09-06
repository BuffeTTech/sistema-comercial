@props(['status'])
@if ($status)
    @php
        $class = "";
        switch($status) {
            case 'ACTIVE':
                $class = "badge badge-md bg-success";
                $name = \App\Enums\UserStatus::getEnumByName($status)->value;
                break;
            case 'UNACTIVE':
                $class = "badge badge-md bg-danger";
                $name = \App\Enums\UserStatus::getEnumByName($status)->value;
                break;
            case 'PENDENT':
                $class = "badge badge-md bg-warning";
                $name = \App\Enums\UserStatus::getEnumByName($status)->value;
                break;
            default:
                $class = "badge badge-md bg-secondary";
                $name = \App\Enums\UserStatus::getEnumByName($status)->value;
                break;
        }
    @endphp

    <span class="{{$class}}">{{ \App\Enums\UserStatus::getEnumByName($status) }}</span>
@endif