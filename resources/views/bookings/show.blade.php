<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 float-left" style="width: 50%; border-right: 3px solid #000000;">
                    <div class="bg-gray-50 border-b-2 border-gray-200">
                        <p><strong>Nome do Aniversariante:</strong> {{ $booking->name_birthdayperson }}</p><br>
                        <p><strong>Idade:</strong> {{ $booking->years_birthdayperson }}</p><br>
                        <p><strong>Número de Convidados:</strong>{{ $booking->num_guests }}</p><br>
                        <p><strong>Dia da Festa:</strong> {{ $booking->party_day }}</p><br>
                        <p><strong>Horário da festa:</strong> {{ $booking->schedule['start_time'] }}</p><br>
                   {{-- <p><strong>Valor do Horário:</strong> {{ $booking->price_scheduçe }}</p><br> --}}
                        <p><strong>Status:</strong><x-status.booking_status :status="$booking->status" /></p>
                            <form action="{{ route('booking.change_status', ['buffet' => $buffet->slug, 'booking' => $booking]) }}" method="post" class="inline">
                                @csrf
                                @method('patch')
            
                                <label for="status" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"></label>
                                <select name="status" id="status" required onchange="this.form.submit()">
                                    @foreach( App\Enums\BookingStatus::array() as $key=>$status )
                                        <option value="{{$status}}" {{ $booking['status'] == $status ? 'selected' : ""}}>{{$key}}</option>
                                    @endforeach
                                </select>
                            </form>
                        <p><strong>Pacote de Comida:</strong> {{ $booking->food->name_food }}</p><br>
                   {{-- <p><strong>Preço do Pacote:</strong> {{ $booking->price_food }}</p><br> --}}
                        <p><strong>Pacote de Decoração:</strong> {{ $booking->decoration->main_theme }}</p><br>
                   {{-- <p><strong>Preço da Decoração:</strong> {{ $booking->price_decoration }}</p><br> --}}
                        <p><strong>Preço:</strong> {{ $booking->price_food + $booking->price_decoration + $booking->price_schedule}}</p><br>
                   {{-- <p><strong>Valor do desconto:</strong> {{ $booking->discount}}</p><br> --}}

                    <div class="flex items-center ml-auto float-down">
                        <a href="{{ route('booking.edit', ['buffet'=>$buffet->slug, 'booking'=>$booking]) }}" class="bg-amber-300 hover:bg-amber-500 text-black font-bold py-2 px-4 rounded">
                            <div class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4">
                                Editar
                            </div>
                        </a>
                    </div>
                    <br>



                    <div><table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <!-- w-24 p-3 text-sm font-semibold tracking-wide text-left -->
                                
                                <th class="w-20 p-3 text-sm font-semibold tracking-wide text-center">ID</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-left">Nome do Convidado</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">CPF</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Idade</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Status</th>

                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if(count($guests) === 0)
                            <tr>
                                <td colspan="8" class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">Nenhuma Convidado Ainda!</td>
                            </tr>
                            @else
                                @foreach($guests as $key=>$guest)
                                <tr class="bg-white">
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $key+1 }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                    <a href="{{ route('guest.show', ['booking'=>$booking['id'], 'buffet'=>$buffet->slug, 'guest'=>$guest['id']]) }}" class="font-bold text-blue-500 hover:underline">{{ $guest->name }}</a>
                                    </td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $guest->document }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $guest->age}}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $guest->status }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                        <a href="{{ route('guest.show', ['booking'=>$booking['id'], 'buffet'=>$buffet->slug, 'guest'=>$guest['id']]) }}" title="Visualizar '{{$guest->name}}'">👁️</a>
                                        {{-- <form action="{{ route('guest.destroy', ['guest'=>$guest['id'], 'buffet'=>$buffet->slug]) }}" method="post" class="inline">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" title="Deletar '{{ $guest['start_time'] }}'">❌</button>
                                        </form> --}}
                                    </td>
                                </tr>
                                @endforeach
                            @endif

                        </tbody>
                    </table>
                </div>
                <br>
                    <div class="flex items-center ml-auto float-down">
                        <a href="{{ route('guest.invite', ['buffet'=>$buffet->slug, 'booking'=>$booking]) }}" class="bg-amber-300 hover:bg-amber-500 text-black font-bold py-2 px-4 rounded">
                            <div class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4">
                                Convidar
                            </div>
                        </a>
                    </div>
            </div>
        </div>
    </div>
</x-app-layout>