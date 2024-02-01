<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-auto">
                        <div>
                            <h1 class="inline-flex items-center border border-transparent text-lg leading-4 font-semi-bold">Listagem das reservas</h1>
                            <h2><a href="{{ route('booking.create', ['buffet'=> $buffet->slug]) }}">Criar Reserva</a></h2>
                            <h2><a href="{{ route('booking.list', ['buffet'=>$buffet->slug])}}">Listar todas reservas</a></h2>
                        </div>
                        @if($current_party)
                            <div>
                                <h2><a href="{{ route('booking.party_mode', ['buffet'=>$buffet->slug])}}"><strong>Acessar Festa em Andamento!</strong></a></h2>

                            </div>
                        @endif
                        {{-- @php
                            $user = auth()->user();
                        @endphp
                        @if(isset($current_party) && ($user->hasRole('operational') || $user->hasRole('administrative')))
                            <div class="bg-amber-400 p-4 text-white rounded-md my-3">
                                <h1>Há uma festa em andamento! <a href="{{route('bookings.party_mode', $current_party->id)}}" class="font-bold">Clique aqui</a></h1>
                            </div>
                        @endif --}}
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <!-- w-24 p-3 text-sm font-semibold tracking-wide text-left -->
                                
                                <th class="p-3 text-sm font-semibold tracking-wide text-left">Nome Aniversariante</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Máx. Convidados</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Comida</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Decoração</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Dia da festa</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Inicio</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Fim</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Status</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Ações</th>

                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if(count($bookings) === 0)
                            <tr>
                                <td colspan="8" class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">Nenhuma reserva encontrado</td>
                            </tr>
                            @else
                                @foreach($bookings as $key=>$booking)
                                <tr class="bg-white">
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                    <a href="{{ route('booking.show', ['booking'=>$booking['hashed_id'], 'buffet'=>$buffet->slug]) }}" class="font-bold text-blue-500 hover:underline">{{ $booking->name_birthdayperson }}</a>
                                    </td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $booking->num_guests }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $booking->food['slug'] }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $booking->decoration['slug'] }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ date('d/m/Y',strtotime($booking->party_day)) }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ date("H:i", strtotime($booking->schedule['start_time'])) }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ date("H:i", strtotime(\Carbon\Carbon::parse($booking->schedule['start_time'])->addMinutes($booking->schedule['duration']))) }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center"><x-status.booking_status :status="$booking['status']" /></td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                        <a href="{{ route('booking.show', ['booking'=>$booking['hashed_id'], 'buffet'=>$buffet->slug]) }}" title="Visualizar '{{$booking->name_birthdayperson}}'">👁️</a>
                                        <a href="{{ route('booking.edit', ['booking'=>$booking['hashed_id'], 'buffet'=>$buffet->slug]) }}" title="Editar '{{$booking->name_birthdayperson}}'">✏️</a>
                                    </td>
                                </tr>
                                @endforeach
                            @endif

                        </tbody>
                    </table>
                    {{ $bookings->links('components.pagination') }}
                    </div>

                </div>
            </div>
            <br><br> 
            <iframe src="{{ route('booking.calendar', ['buffet'=>$buffet->slug]) }}" frameborder="1" width="1000px" height="700px"></iframe>

        </div>
    </div>
</x-app-layout>