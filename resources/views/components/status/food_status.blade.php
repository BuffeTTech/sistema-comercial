@props(['status'])

@if ($status)
    @php
        $class = "";
        switch($status) {
            case 'ACTIVE':
                $class = "p-1.5 text-xs font-medium uppercase tracking-wider text-green-800 bg-green-200 rounded-lg bg-opacity-50";
                break;
            case 'UNACTIVE':
                $class = "p-1.5 text-xs font-medium uppercase tracking-wider text-red-800 bg-red-200 rounded-lg bg-opacity-50";
            break;
            default:
                $class = "p-1.5 text-xs font-medium uppercase tracking-wider text-gray-800 bg-gray-400 rounded-lg bg-opacity-50";
                break;
        }
    @endphp

    <span class="{{$class}}">{{ \App\Enums\FoodStatus::getEnumByName($status) }}</span>
@endif