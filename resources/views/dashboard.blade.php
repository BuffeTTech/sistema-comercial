<x-layouts.all_buffets>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Central') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1>Meus Buffets</h1>
                    <br>
                    @foreach($buffets as $buffet)
                        <div class="bg-gray-500">
                            <h2>{{ $buffet->trading_name }}</h2>
                            <a href="{{ route('buffet.dashboard', ['buffet'=>$buffet->slug]) }}">Ver buffet</a>
                            |
                            <a href="">Editar buffet</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.all_buffets>