@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Reserva', 'subtitle'=>'Criar Reserva'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Criar Reserva</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive px-4">
                            <form method="POST" action="{{ route('booking.store', ['buffet'=>$buffet->slug]) }}">
                                @csrf
                                <div class="form-group">
                                    <label for="name_birthdayperson" class="form-control-label">Nome do Aniversariante</label>
                                    <input class="form-control" type="text" placeholder="Guilherme" id="name_birthdayperson" name="name_birthdayperson" value="{{ old('name_birthdayperson') }}">
                                    <x-input-error :messages="$errors->get('name_birthdayperson')" class="mt-2" />
                                </div>

                                <div class="form-group">
                                    <label for="years_birthdayperson" class="form-control-label">Idade do Aniverasariante</label>
                                    <input class="form-control" type="years_birthdayperson" placeholder="20" id="years_birthdayperson" name="years_birthdayperson" value="{{ old('years_birthdayperson') }}">
                                    <x-input-error :messages="$errors->get('years_birthdayperson')" class="mt-2" />
                                </div>

                                <div class="form-group">
                                    <label for="num_guests" class="form-control-label">Número de convidados</label>
                                    <input class="form-control" type="num_guests" placeholder="50" id="num_guests" name="num_guests" value="{{ old('num_guests') }}">
                                    <x-input-error :messages="$errors->get('num_guests')" class="mt-2" />
                                </div>

                                <div class="form-group">
                                    <label for="food_id" class="form-control-label">Pacotes de Comidas</label>
                                    <select name="food_id" id="food_id" class="form-control" >
                                        @foreach($foods as $food)
                                            <option value="{{$food->slug}}">{{ $food->name_food }}</option>
                                        @endforeach
                                    </select>
                                    <span class="block">
                                        R$: <span class="font-bold text-xl">{{number_format((float) $food['price'], 2)}}</span> p/ pessoa
                                    </span>
                                    <x-input-error :messages="$errors->get('food_id')" class="mt-2" />
                                </div>

                                <div class="form-group">
                                    <label for="decoration_id" class="form-control-label">Pacotes de Decorações</label>
                                    <select name="decoration_id" id="decoration_id" class="form-control" >
                                        @foreach($decorations as $decoration)
                                            <option value="{{$decoration->slug}}">{{ $decoration->main_theme}}</option>
                                        @endforeach
                                    </select>
                                    <span class="block">
                                        R$: <span class="font-bold text-xl">{{number_format((float) $decoration['price'], 2)}}</span> p/ pessoa
                                    </span>
                                    <x-input-error :messages="$errors->get('decoration_id')" class="mt-2" />
                                </div>

                                <div class="form-group">
                                    <label for="party_day" class="form-control-label">Data</label>
                                    <input class="form-control" type="date" id="party_day" name="party_day">
                                    <x-input-error :messages="$errors->get('party_day')" class="mt-2" />
                                </div>

                                <div class="form-group">
                                    <label for="schedule_id" class="form-control-label">Horários disponíveis</label>
                                    <select name="schedule_id" id="schedule_id" class="form-control" >
                                        <option value="invalid" selected disabled>Selecione um horario disponível</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('schedule_id')" class="mt-2" />
                                </div>

                                <button class="btn btn-primary" type="submit">Cadastrar Reserva</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
@endsection
<script>
    // variaveis
    document.addEventListener('DOMContentLoaded', (event) => {
        const SITEURL = "{{ url('/') }}";
        const csrf = document.querySelector('meta[name="csrf-token"]').content
        
        const party_day = document.querySelector("#party_day")
        const party_time = document.querySelector("#schedule_id")
    
        party_day.addEventListener('change', async function() {
            const agora = new Date();
            const escolhida = new Date(this.value + 'T00:00:00');
            while (party_time.options.length > 1) {
                party_time.remove(1); // Remova a segunda opção em diante (índice 1)
            }
            agora.setDate(agora.getDate() + 5);
            if (escolhida < agora) {
                const data = agora.toISOString().split('T')[0]
                this.value = data;
                error(`Você só pode marcar festas após 5 dias contados a partir da data de hoje (${data}).`)
            }

            const dates = await getDates(this.value)

            printDates(dates)

        });

        async function getDates(day) {
            const csrf = document.querySelector('meta[name="csrf-token"]').content
            const data = await axios.get(SITEURL + '/api/{{$buffet->slug}}/booking/schedule/' + day, {
                headers: {
                    'X-CSRF-TOKEN': csrf
                }
            })

            return data.data;
        }

        function printDates(dates) {
            const schedules = dates.schedules
            const options = schedules.map((date) => {
                const party_date = new Date("1970-01-01T" + date.start_time + "Z");
                party_date.setMinutes(party_date.getMinutes() + date.duration);
                var horaFinal = party_date.toISOString().substr(11, 8);
                return {
                    msg: `${date.start_time} - ${horaFinal}`,
                    value: date.id
                }
            })

            for (let i = 0; i < options.length; i++) {
                const option = document.createElement("option");
                option.text = options[i].msg;
                option.value = options[i].value
                party_time.appendChild(option);
            }
        }
    })

</script>