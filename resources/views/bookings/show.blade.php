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
            </div>
        </div>
    </div>
</x-app-layout>