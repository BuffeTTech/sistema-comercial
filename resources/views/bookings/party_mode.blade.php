<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if($booking)
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between">
                        
                        <div style="width: 47%">
                            
                            <div class="border-b-2 border-gray-200">
                                <h1>
                                    <strong>Nome do aniversariante:</strong>
                                </h1>
                                <h2>{{$booking->name_birthdayperson}}</h2>
                                <br>
                                
                                    @php
                                    $total_guests_present = $guest_counter['present'] + $guest_counter['extras'];
                                    $total_guests_stipulated = $guest_counter['unblocked'] - $guest_counter['extras'];
                                    @endphp
                                <h1><strong>Quantidade de convidados que chegaram:</strong></h1>
                                <h2>{{$total_guests_present}} de {{$total_guests_stipulated}} (convidados pagos {{ $booking->num_guests}})</h2>
                                {{-- <h2><strong>Quantidade de convidados que chegaram:</strong> {{$total_guests_present}}</h2>
                                <h2><strong>Quantidade de convidados estipulados:</strong> {{$total_guests_stipulated}}</h2>
                                <h2><strong>Quantidade de convidados pagos:</strong> {{ $booking->num_guests}}</h2> --}}
                            </div>

                            <div>
                                <div class="border-gray-200">
                                    <p><strong>{{ $booking->food['name_food'] }}</strong></p>
                                    <br>
                                    <p><strong>Slug:</strong> {{ $booking->food['slug'] }}</p>
                                    <p><strong>Preço p/pessoa:</strong> R${{ $booking->food['price'] }}</p>
                                    <br>
                                    <p><strong>Descricao das comidas:</strong></p>
                                    {!! $booking->food['food_description'] !!}
                                    <br>
                                    <br>
                                    <p><strong>Descricao das bebidas:</strong></p>
                                    {!! $booking->food['beverages_description'] !!}
                                    <p><strong>Decoração:</strong></p>
                                    {!! $booking->decoration['main_theme'] !!}
                                    <p><strong>Preço p/pessoa:</strong> R${{ $booking->decoration['price'] }}</p>
                                    <br>
                                    <p><strong>Preço final:</strong> {{ $booking->price_food * $booking->num_guests + $booking->price_decoration * $booking->num_guests + $booking->price_schedule  * $booking->num_guests}}</p><br>
                                </div>
                            </div>

                        </div>

                        <div style="width: 50%">
                            <div class="border-gray-200">
                                @if ($errors->any())
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}
                                    @endforeach
                                @endif
            
                                <h1><strong>Convidados Extras:</strong></h1>
            
                                <form class="w-full max-w-lg" method="POST" action="{{ route('guest.store', ['buffet'=>$buffet->slug, 'booking'=>$booking->hashed_id]) }}" enctype="multipart/form-data" id="form">
                                    <x-input-error :messages="$errors->get('message')" class="mt-2" />
            
                                    @csrf

                                    {{-- <input type="hidden" name="booking_id" value="{{ $booking->hashed_id }}"> --}}
                                    <input type="hidden" name="status" id= 'status' value={{App\Enums\GuestStatus::EXTRA->name}}>
            
                                    <div id="form-rows">
                                            <div class="form-row mb-4">
                                                <x-input-label for="name" :value="__('Nome')" class="dark:text-slate-800" />
                                                <x-text-input id="name" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" placeholder="Nome" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                            </div>
                
                                            <div class="form-row mb-4">
                                                <x-input-label for="document" :value="__('CPF')" class="dark:text-slate-800"/>
                                                <x-text-input id="document" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" placeholder="CPF" type="text" name="document" :value="old('document')" required autofocus autocomplete="document" />
                                                <x-input-error :messages="$errors->get('document')" class="mt-2" />
                                                <span class="text-sm text-red-600 dark:text-red-400 space-y-1" id="document-error"></span>
                                            </div>
                
                                            <div class="form-row mb-4">
                                                <x-input-label for="age" :value="__('Idade')" class="dark:text-slate-800"/>
                                                <x-text-input id="age" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" placeholder="Idade" type="number" name="age" :value="old('age')" required autofocus autocomplete="age" />
                                                <x-input-error :messages="$errors->get('age')" class="mt-2" />
                                            </div> 
                                    </div>
                                    
                                    <div class="flex items-center justify-end mt-4">
                                        <x-primary-button class="ms-4">
                                            {{ __('Adcionar Convidado') }}
                                        </x-primary-button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="text-gray-900">
                    <div>
                        @if($guest_counter === 0)
                            <h1><strong>ESSA FESTA NÃO TEM CONVIDADOS CONFIRMADOS!</strong></h1>
                        
                        @else
                            <div class="p-6 text-gray-900">
                                <h1><strong>Lista de convidados</strong></h1>
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                                        <tr>
                                            <!-- w-24 p-3 text-sm font-semibold tracking-wide text-left -->
                                            
                                            <th class="p-3 text-sm font-semibold tracking-wide text-left">Nome</th>
                                            <th class="p-3 text-sm font-semibold tracking-wide text-center">CPF</th>
                                            <th class="p-3 text-sm font-semibold tracking-wide text-center">Idade</th>
                                            <th class="p-3 text-sm font-semibold tracking-wide text-center">Status</th>
                                            <th class="p-3 text-sm font-semibold tracking-wide text-center">Ações</th>

                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @if(count($guests) === 0)
                                        <tr>
                                            <td colspan="8" class="p-3 text-sm text-gray-700 whitespace-nowrap text-left">Nenhum convidado encontrado</td>
                                        </tr>
                                        @else
                                            @php
                                                $limite_char = 30; // O número de caracteres que você deseja exibir
                                            @endphp
                                            @foreach($guests as $key=>$value)
                                            <tr class="bg-white">
                                                <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-left">{{ $value['name'] }}</td>
                                                <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ mb_strimwidth($value['document'], 0, $limite_char, " ...") }}</td>
                                                <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ (int)$value['age'] }}</td>
                                                <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center"><x-status.guest_status :status="$value['status']" /></td>
                                                <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                                    @if($booking->status == App\Enums\BookingStatus::APPROVED->name)

                                                        @if($value->status == App\Enums\GuestStatus::PENDENT->name)
                                                            <form action="{{ route('guest.change_status', ['buffet' => $buffet->slug, 'guest' => $value['hashed_id'], 'booking' => $booking->hashed_id]) }}" method="POST" class="inline">
                                                                @csrf
                                                                @method('PATCH')

                                                                <input type="hidden" name="status" value="{{App\Enums\GuestStatus::CONFIRMED->name}}">
                                                                <button type="submit" title="Confirmar '{{$value->name}}'">✅</button>
                                                            </form>
                                                            <form action="{{ route('guest.change_status', ['buffet' => $buffet->slug, 'guest' => $value['hashed_id'], 'booking' => $booking->hashed_id]) }}" method="POST" class="inline">
                                                                @csrf
                                                                @method('PATCH')

                                                                <input type="hidden" name="status" value="{{App\Enums\GuestStatus::BLOCKED->name}}">
                                                                <button type="submit" title="Bloquear '{{$value->name}}'">❌</button>
                                                            </form>
                                                        @elseif($value->status == App\Enums\GuestStatus::CONFIRMED->name)
                                                            <form action="{{ route('guest.change_status', ['buffet' => $buffet->slug, 'guest' => $value['hashed_id'], 'booking' => $booking->hashed_id]) }}" method="POST" class="inline">
                                                                @csrf
                                                                @method('PATCH')

                                                                @if($total_guests_present >= $booking->num_guests)
                                                                    <input type="hidden" name="status" value="{{App\Enums\GuestStatus::EXTRA->name}}">
                                                                @else
                                                                    <input type="hidden" name="status" value="{{App\Enums\GuestStatus::PRESENT->name}}">
                                                                @endif
                                                                <button type="submit" title="Confirmar Presença '{{$value->name}}'">✅</button>
                                                            </form>
                                                            <form action="{{ route('guest.change_status', ['buffet' => $buffet->slug, 'guest' => $value['hashed_id'], 'booking' => $booking->hashed_id]) }}" method="POST" class="inline">
                                                                @csrf
                                                                @method('PATCH')

                                                                <input type="hidden" name="status" value="{{App\Enums\GuestStatus::ABSENT->name}}">
                                                                <button type="submit" title="Confirmar Ausência '{{$value->name}}'">❌</button>
                                                            </form>
                                                        @endif
                                                    @else
                                                        <x-status.guest_status :status="$guest['status']" />
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <script>
                    const doc = document.querySelector("#document")
                    const doc_error = document.querySelector("#document-error")
                    const form = document.querySelector("#form")
                    //const button = document.querySelector("#button");
            
                    form.addEventListener('submit', async function (e) {
                        e.preventDefault()
                        const cpf_valid = validarCPF(doc.value)
                        if(!cpf_valid) {
                            error('Documento inválido')
                            return
                        }
            
                        const userConfirmed = await confirm(`Deseja cadastrar este convidado?`)
            
                        if (userConfirmed) {
                            this.submit();
                        } else {
                            error("Ocorreu um erro!")
                            return;
                        }
                    })
            
                    doc.addEventListener('input', (e)=>{
                        e.target.value = replaceCPF(e.target.value);
                        return;
                    })
            
                    doc.addEventListener('focusout', (e)=>{
                        const cpf_valid = validarCPF(doc.value)
                        if(!cpf_valid) {
                            //button.disabled = true;
                            doc_error.innerHTML = "Documento inválido"
                            return
                        }
                        doc_error.innerHTML = ""
                        //button.disabled = false;
                        return;
                    })
                </script>

                @else
                    <h1>Nao existe festa em andamento</h1>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>