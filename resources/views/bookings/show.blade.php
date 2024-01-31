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
                        <a href="{{ route('booking.edit', ['buffet'=>$buffet->slug, 'booking'=>$booking->hashed_id]) }}" class="bg-amber-300 hover:bg-amber-500 text-black font-bold py-2 px-4 rounded">
                            <div class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4">
                                Editar
                            </div>
                        </a>
                    </div>
                    <form action="{{ route('booking.change_status', ['buffet' => $buffet->slug, 'booking' => $booking->hashed_id]) }}" method="post" class="inline">
                        @csrf
                        @method('patch')
                        <input type="hidden" name="status" value="{{App\Enums\BookingStatus::CANCELED->name}}">
                        <button type="submit" title="Cancelar festa" class="bg-amber-300 hover:bg-amber-500 text-black font-bold py-2 px-4 rounded">Cancelar festa</button>
                    </form>
                    <br>



                    <div><table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <!-- w-24 p-3 text-sm font-semibold tracking-wide text-left -->
                                
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
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $guest->name }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $guest->document }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $guest->age}}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                        <form action="{{ route('guest.change_status', ['buffet' => $buffet->slug, 'guest' => $guest['id'], 'booking' => $booking]) }}" method="post" class="inline">
                                            @csrf
                                            @method('patch')
                        
                                            <label for="status" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"></label>
                                            <select name="status" id="status" required onchange="this.form.submit()">
                                                @foreach( App\Enums\GuestStatus::array() as $key=>$status )
                                                    <option value="{{$status}}" {{ $guest['status'] == $status ? 'selected' : ""}}>{{$key}}</option>
                                                @endforeach
                                                <!-- <option value="invalid2"  disabled>Nenhum horario disponivel neste dia, tente novamente!</option> -->
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            @endif

                        </tbody>
                    </table>
                </div>
                <br>
                <div>
                    <input type="text" name="texto" id="copy-input" value="{{ route('guest.invite', ['buffet'=>$buffet->slug, 'booking'=>$booking->hashed_id]) }}" readonly/> 
                    <button type="button" id="button" class="border-none h-100 text-white bg-amber-300 sm:px-2 sm:py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30px" height="30px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                    </button> 
                </div>
                    {{-- <div class="flex items-center ml-auto float-down">
                        <a href="{{ route('guest.invite', ['buffet'=>$buffet->slug, 'booking'=>$booking]) }}" class="bg-amber-300 hover:bg-amber-500 text-black font-bold py-2 px-4 rounded">
                            <div class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4">
                                Convidar
                            </div>
                        </a>
                    </div> --}}
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    const btn = document.querySelector('#button')
    if (btn) {
        btn.addEventListener('click', (e) => {
            copiarTexto()
        })
    }
    async function copiarTexto() {
        let textoCopiado = document.getElementById("copy-input");
        textoCopiado.select();
        textoCopiado.setSelectionRange(0, 99999)
        document.execCommand("copy");
        await basic(`Link copiado para a área de transferencia`)
    }
</script>