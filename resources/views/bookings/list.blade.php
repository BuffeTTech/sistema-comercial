<x-app-layout >

   {{-- @php
        $user = auth()->user();
    @endphp
    --}} 
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-auto">
                        <div class="flex justify-between">
                            <h1 class="inline-flex items-center border border-transparent text-lg leading-4 font-semi-bold">Listagem de todas as reservas {{$format == 'pendent' ? 'pendentes' : ''}}</h1>
                            <a href="?format={{$format == 'pendent' ? 'all' : 'pendent'}}" class="text-black-300 bg-amber-300 hover:bg-amber-500 hover:text-black rounded-md px-3 py-2 text-sm font-medium">Ver reservas {{$format == 'pendent' ? '' : 'pendentes'}}</a>
                        </div>
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <!-- w-24 p-3 text-sm font-semibold tracking-wide text-left -->
                                <th class="w-20 p-3 text-sm font-semibold tracking-wide text-center">ID</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-left">Nome Aniversariante</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Máx. Convidados</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Comida</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Decoração</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Dia da festa</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Inicio</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Fim</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Status</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Ações</th>
                                {{--@if($format == 'all')
                                     @role('administrative')
                                        <th class="p-3 text-sm font-semibold tracking-wide text-center">Alterar Status</th>
                                    @endrole
                                @endif
                                --}}
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if($bookings->total() === 0)
                            <tr>
                                <td colspan="8" class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">Nenhuma reserva encontrada</td>
                            </tr>
                            @else   
                            @foreach($bookings->items() as $booking)
                                <tr class="bg-white">
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $booking['id'] }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                    <a href="{{ route('booking.show', ['booking'=>$booking['id'], 'buffet'=>$buffet->slug]) }}" class="font-bold text-blue-500 hover:underline">{{ $booking->name_birthdayperson }}</a>
                                    </td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $booking->num_guests }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $booking->food['slug'] }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $booking->decoration['slug'] }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ date('d/m/Y',strtotime($booking->party_day)) }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ date("H:i", strtotime($booking->schedule['start_time'])) }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ date("H:i", strtotime(\Carbon\Carbon::parse($booking->schedule['start_time'])->addMinutes($booking->schedule['duration']))) }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center"><x-status.booking_status :status="$booking['status']" /></td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                        <a href="{{ route('booking.show', ['booking'=>$booking['id'], 'buffet'=>$buffet->slug]) }}" title="Visualizar '{{$booking->name_birthdayperson}}'">👁️</a>
                                        <a href="{{ route('booking.edit', ['booking'=>$booking['id'], 'buffet'=>$buffet->slug]) }}" title="Editar '{{$booking->name_birthdayperson}}'">✏️</a>
                                    </td>
                                </tr>
                                    {{-- @if($format === 'all')
                                        @role('administrative')
                                        <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                                <form method="POST">
                                                    @csrf
                                                    @method('PATCH')

                                                    <label for="status" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"></label>
                                                    <select name="status" id="status" required onchange="this.form.submit()">
                                                        @foreach( App\Enums\BookingStatus::array() as $key => $value )
                                                            <option value="{{$value}}" {{ $booking->status == $value ? 'selected' : ""}}>{{$key}}</option>
                                                        @endforeach
                                                        <!-- <option value="invalid2"  disabled>Nenhum horario disponivel neste dia, tente novamente!</option> -->
                                                    </select>
                                                </form>
                                        </td>
                                        @endrole
                                    @endif --}}
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                        @php
                                            // $date = \Illuminate\Support\Carbon::parse($booking->party_day.' '.$booking->open_schedule['time']);
                                            // $date = $date->subDays($min_days);
                                        @endphp
                                        {{-- @if($booking->status === 'Pendente' && $format == 'pendent')
                                            <form action="{{ route('booking.changeStatus', $booking->id) }}" method="post" class="inline">
                                                @csrf
                                                @method('patch')
                                                <input type="hidden" name="status" value="{{App\Enums\BookingStatus::APPROVED->name}}">
                                                <button type="submit" title="Aprovar festa '{{$booking->name_birthdayperson}}'">✅</button>
                                            </form>
                                            <form action="{{ route('booking.negar', $booking->id) }}" method="post" class="inline">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" title="Negar festa '{{$booking->name_birthdayperson}}'">❌</button>
                                            </form>
                                        @endif --}}

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
        </div>
    </div>
</x-app-layout>