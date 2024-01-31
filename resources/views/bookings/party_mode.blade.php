<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if($booking)
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between">
                            <div style="width: 50%">
                                <div class="border-b-2 border-gray-200">
                                    <h1>
                                        <strong>Nome do aniversariante:</strong>
                                    </h1>
                                    <h2>{{$booking->name_birthdayperson}}</h2>
                                    <br>
                                    <h1>
                                        <strong>Quantidade de convidados que chegaram:</strong>
                                    </h1>
                                    <h2>{{$guest_counter['present']}} de {{$guest_counter['unblocked']}}</h2>
                                </div>
                                <div>
                                    <!-- show package -->
                                    <div style="padding-bottom: 10%;" class="border-gray-200">
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
                
                                    <form class="w-full max-w-lg" method="POST" action="{{ route('guest.store', ['buffet'=>$buffet, 'booking'=>$booking->hashed_id]) }}" enctype="multipart/form-data" id="form">
                
                                        @csrf

                                        {{-- <input type="hidden" name="booking_id" value="{{ $booking->hashed_id }}"> --}}
                                        <input type="hidden" name="status" id= 'status' value={{App\Enums\GuestStatus::PRESENT->name}}>
                
                                        <div id="form-rows">
                                            <div class="form-row">
                                                <div class="flex flex-wrap -mx-3 mb-6">
                                                    <div class="w-full px-3 mb-6 md:mb-0">
                                                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="name">
                                                            Nome do convidado
                                                        </label>
                                                        <input required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="name" type="text" placeholder="name do Convidado" name="name" value="{{old('name')}}">
                                                    </div>
                                                </div>
                                                <div class="flex flex-wrap -mx-3 mb-6">
                                                    <div class="w-full px-3 mb-6 md:mb-0">
                                                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="document">
                                                            CPF
                                                        </label>
                                                        <input required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white cpfs" id="document" type="text" placeholder="document do Convidado" name="document" value="{{old('document')}}" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" title="Digite um CPF válido (XXX.XXX.XXX-XX)">
                                                    <span class="text-sm text-red-600 dark:text-red-400 space-y-1" id="document-error"></span>
                                                </div>
                                                </div>
                                                <div class="flex flex-wrap -mx-3 mb-6">
                                                    <div class="w-full  px-3 mb-6 md:mb-0">
                                                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="age">
                                                            Idade 
                                                        </label>
                                                    <input required type="number" id="age" name="age" placeholder="Idade do Convidado">{{old('age')}}
                                                    </div>
                                                </div>
                    
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
                                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Aterar Status</th>
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
                                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center"><x-status.guest_status :status="$value->status" /></td>
                                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                                        <div class="flex flex-wrap -mx-3 mb-6">
                                                        <div class="w-full  px-3 mb-6 md:mb-0">

                                                            <form action="{{ route('guest.change_status', ['buffet' => $buffet, 'guest' => $value['id'], 'booking' => $booking]) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')

                                                                <label for="status" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"></label>
                                                                <select name="status" id="status" required onchange="this.form.submit()">
                                                                    @foreach( App\Enums\GuestStatus::array() as $key => $status )
                                                                        <option value="{{$status}}" {{ $value->status == $status ? 'selected' : ""}}>{{$key}}</option>
                                                                    @endforeach
                                                                    <!-- <option value="invalid2"  disabled>Nenhum horario disponivel neste dia, tente novamente!</option> -->
                                                                </select>
                                                            </form>
                                                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="status">
                            
                                                            <!-- <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" /> -->
                                                        </div>
                                                </tr>
                                                @endforeach
                                            @endif

                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <h1>Nao existe festa em andamento</h1>
                @endif
            </div>
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

            const userConfirmed = await confirm(`Deseja cadastrar este funcionário?`)

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

</x-app-layout>