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
                                        <p><strong>{{ $booking->package['name_package'] }}</strong></p>
                                        <br>
                                        <p><strong>Slug:</strong> {{ $booking->package['slug'] }}</p>
                                        <p><strong>Preço p/pessoa:</strong> R${{ $booking->package['price'] }}</p>
                                        <br>
                                        <p><strong>Descricao das comidas:</strong></p>
                                        {!! $booking->package['food_description'] !!}
                                        <br>
                                        <br>
                                        <p><strong>Descricao das bebidas:</strong></p>
                                        {!! $booking->package['beverages_description'] !!}
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
                
                                    <form class="w-full max-w-lg" method="POST" enctype="multipart/form-data" id="form">
                
                                        @csrf
                
                                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                
                                        <div id="form-rows">
                                            <div class="form-row">
                                                <div class="flex flex-wrap -mx-3 mb-6">
                                                    <div class="w-full px-3 mb-6 md:mb-0">
                                                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="nome">
                                                            Nome do convidado
                                                        </label>
                                                        <input required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="nome" type="text" placeholder="Nome do Convidado" name="rows[0][nome]" value="{{old('nome')}}">
                                                    </div>
                                                </div>
                                                <div class="flex flex-wrap -mx-3 mb-6">
                                                    <div class="w-full px-3 mb-6 md:mb-0">
                                                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="cpf">
                                                            CPF
                                                        </label>
                                                        <input required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white cpfs" id="cpf" type="text" placeholder="CPF do Convidado" name="rows[0][cpf]" value="{{old('cpf')}}" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" title="Digite um CPF válido (XXX.XXX.XXX-XX)">
                                                    </div>
                                                </div>
                                                <div class="flex flex-wrap -mx-3 mb-6">
                                                    <div class="w-full  px-3 mb-6 md:mb-0">
                                                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="idade">
                                                            Idade 
                                                        </label>
                                                    <input required type="number" id="idade" name="rows[0][idade]" placeholder="Idade do Convidado">{{old('idade')}}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                                            Adicionar convidado
                                        </button>
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
                                                
                                                <th class="w-20 p-3 text-sm font-semibold tracking-wide text-center">ID</th>
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
                                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $value['id'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-left">{{ $value['nome'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ mb_strimwidth($value['cpf'], 0, $limite_char, " ...") }}</td>
                                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ (int)$value['idade'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center"><x-status.guest_status :status="$guest->status" /></td>
                                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                                        <div class="flex flex-wrap -mx-3 mb-6">
                                                        <div class="w-full  px-3 mb-6 md:mb-0">

                                                            <form action="{{route('guests.updateStatus',$value['id'])}}" method="POST">
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
                                    {{ $guests->links('components.pagination') }}
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
        const form = document.querySelector("#form")

        function validarCPF(cpf) {
            cpf = cpf.replace(/[^\d]/g, '');

            if (cpf.length !== 11) {
                return false;
            }

            if (/^(\d)\1+$/.test(cpf)) {
                return false;
            }

            let soma = 0;
            for (let i = 0; i < 9; i++) {
                soma += parseInt(cpf.charAt(i)) * (10 - i);
            }
            let digito1 = 11 - (soma % 11);
            digito1 = (digito1 >= 10) ? 0 : digito1;

            if (parseInt(cpf.charAt(9)) !== digito1) {
                return false;
            }

            soma = 0;
            for (let i = 0; i < 10; i++) {
                soma += parseInt(cpf.charAt(i)) * (11 - i);
            }
            let digito2 = 11 - (soma % 11);
            digito2 = (digito2 >= 10) ? 0 : digito2;

            if (parseInt(cpf.charAt(10)) !== digito2) {
                return false;
            }

            return true;
        }

        form.addEventListener('submit', async function (e){
            e.preventDefault()
            const cpf = document.querySelector('#cpf')

            const cpf_valid = validarCPF(cpf.value)
            if(!cpf_valid) {
                error("O cpf é invalido")
                return;
            }
            const userConfirmed = await confirm(`Deseja cadastrar este usuário na festa?`)

            if (userConfirmed) {
                this.submit();
                basic("Usuário cadastrado com sucesso")
            } else {
                error("Ocorreu um erro!")
            }
        })
    </script>

</x-app-layout>