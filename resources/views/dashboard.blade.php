<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- <div class="flex items-center ml-auto float-down">
                        <a href="{{ route('bookings.index', ['buffet'=>$buffet->slug]) }}" class="bg-amber-300 hover:bg-amber-500 text-black font-bold py-2 px-4 rounded">
                            <div class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4">
                                Bookings
                            </div>
                    <div class="flex items-center ml-auto float-down">
                        <a href="{{ route('food.index', ['buffet'=>$buffet->slug]) }}" class="bg-amber-300 hover:bg-amber-500 text-black font-bold py-2 px-4 rounded">
                            <div class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4">
                                Food Packeages
                            </div>
                    <div class="flex items-center ml-auto float-down">
                        <a href="{{ route('decoration.index', ['buffet'=>$buffet->slug]) }}" class="bg-amber-300 hover:bg-amber-500 text-black font-bold py-2 px-4 rounded">
                            <div class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4">
                                Decorações
                            </div> --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
