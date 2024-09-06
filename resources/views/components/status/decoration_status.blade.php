@props(['status'])
@if ($status)
    @php
        $class = "";
        switch($status) {
            case 'ACTIVE':
                $class = "badge badge-md bg-success";
                $name = \App\Enums\DecorationStatus::getEnumByName($status)->value;
                break;
            case 'UNACTIVE':
                $class = "badge badge-md bg-danger";
                $name = \App\Enums\DecorationStatus::getEnumByName($status)->value;
                break;
            default:
                $class = "badge badge-md bg-secondary";
                $name = "Desconhecido";
                break;
        }
    @endphp
    <span class="{{$class}}">{{ $name }}</span>
@endif