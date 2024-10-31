@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Reserva', 'subtitle'=>'Criar Reserva'])
    <link rel="stylesheet"href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <style>
        .input-radio input[type=radio] {
            display: none;
        }

        .input-radio input[type=radio]:checked~label {
            background-color: #FB6340;
            color: white;
        }

        /* .swiper-button-prev{
            color: black;
            margin: -12px;
        }

        .swiper-button-next{
            color: black;
            margin: -13px;
        } */
    </style>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Criar Reserva</h6>
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive px-4">
                            <form method="POST" action="{{ route('booking.store', ['buffet'=>$buffet->slug]) }}" id="form">
                                @csrf
                                <div class="form-group">
                                    <label for="name_birthdayperson" class="form-control-label">Nome do Aniversariante</label>
                                    <input  class="form-control" type="text" placeholder="Guilherme" id="name_birthdayperson" name="name_birthdayperson" value="{{ old('name_birthdayperson') }}" required>
                                    <x-input-error :messages="$errors->get('name_birthdayperson')" class="mt-2" />
                                </div>

                                <div class="form-group">
                                    <label for="years_birthdayperson" class="form-control-label">Idade do Aniverasariante</label>
                                    <input required class="form-control" type="number" placeholder="20" id="years_birthdayperson" name="years_birthdayperson" value="{{ old('years_birthdayperson') }}">
                                    <x-input-error :messages="$errors->get('years_birthdayperson')" class="mt-2" />
                                </div>

                                <div class="form-group">
                                    <label for="num_guests" class="form-control-label">Número de convidados</label>
                                    <input required class="form-control" type="number" placeholder="50" id="num_guests" name="num_guests" value="{{ old('num_guests') }}">
                                    <x-input-error :messages="$errors->get('num_guests')" class="mt-2" />
                                </div>

                                <div style="position: relative;">
                                    <label for="num_guests" class="form-control-label">Pacote de comidas</label>
                                    {{-- <x-text-input id="food_id" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="date" name="food_id" :value="old('food_id')" required autofocus placeholder="Dia da festa" /> --}}
                                    <div class="food_slider">
                                        <!-- Additional required wrapper -->
                                        <div class="swiper-wrapper">
                                            <!-- Slides -->
                                            @if(count($foods) === 0)
                                            <h1>Nenhum pacote de comida encontrado!</h1>
                                            @else
                                            @foreach($foods as $key => $food)
        
                                            <div class="swiper-slide input-radio p-2 max-w-xl rounded overflow-hidden shadow-lg d-flex justify-content-center align-items-center">
                                                <input {{ $key === 0 ? "required" : "" }} type="radio" name="food_id" id="food-{{$food['slug']}}" value="{{$food['slug']}}" {{ old('food_id') == $food->slug ? 'checked="true"' : ''}}>
                                                <label for="food-{{$food['slug']}}" class="px-6 py-4 bg-amber-100 block w-100 h-100 m-0  d-flex justify-content-center align-items-center flex-column">
                                                    <div>
                                                        <span class="font-bold block text-lg">
                                                            {{$food['name_food']}}
                                                        </span>
                                                        <span class="block">
                                                            R$: <span class="font-bold text-xl">{{number_format((float) $food['price'], 2)}}</span> p/ pessoa
                                                        </span>
                                                    </div>
                                                    <button id='button-food-{{$food['slug']}}'class="see-food-details-button btn btn-secondary block">Ver detalhes</button>
                                                </label>
                                            </div>
                                            @endforeach
                                            @endif
                                        </div>
                                        <!-- If we need pagination -->
                                        <div class="swiper-pagination"></div>
        
                                        <!-- If we need navigation buttons -->
                                            <div class="swiper-button-prev"></div>
                                            <div class="swiper-button-next"></div>
        
                                    </div>
                                    <x-input-error :messages="$errors->get('food_id')" class="mt-2" />
                                </div>

                                <div style="position: relative">
                                    <label for="num_guests" class="form-control-label">Pacote de decoração</label>
                                    {{-- <x-text-input id="decoration_id" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="date" name="decoration_id" :value="old('decoration_id')" required autofocus placeholder="Dia da festa" /> --}}
                                    <div class="decoration_slider">
                                        <!-- Additional required wrapper -->
                                        <div class="swiper-wrapper">
                                            <!-- Slides -->
                                            @if(count($decorations) === 0)
                                            <h1>Nenhum pacote de comida encontrado!</h1>
                                            @else
                                            @foreach($decorations as $key => $decoration)
        
                                            <div class="swiper-slide input-radio p-2 max-w-xl rounded overflow-hidden shadow-lg d-flex justify-content-center align-items-center">
                                                <input {{ $key === 0 ? "required" : "" }} type="radio" name="decoration_id" id="decoration-{{$decoration['slug']}}" value="{{$decoration['slug']}}" class="px-8 py-8" {{ old('decoration_id') == $decoration->slug ? 'checked="true"' : ''}}>
                                                <label for="decoration-{{$decoration['slug']}}" class="px-6 py-4 bg-amber-100 block w-100 h-100 m-0  d-flex justify-content-center align-items-center flex-column">
                                                    <span class="font-bold block text-lg">
                                                        {{$decoration['main_theme']}}
                                                    </span>
                                                    <span class="block">
                                                        R$: <span class="font-bold text-xl">{{number_format((float) $decoration['price'], 2)}}</span> p/ pessoa
                                                    </span>
                                                    <button id='button-decoration-{{$decoration['slug']}}'class="see-decoration-details-button btn btn-secondary block">Ver detalhes</button>
                                                </label>
                                            </div>
                                            @endforeach
                                            @endif
                                        </div>
                                        <!-- If we need pagination -->
                                        <div class="swiper-pagination"></div>
        
                                        <!-- If we need navigation buttons -->
                                            <div class="swiper-button-prev"></div>
                                            <div class="swiper-button-next"></div>
        
                                    </div>
                                    <x-input-error :messages="$errors->get('decoration_id')" class="mt-2" />
                                </div>

                                <div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="party_day" class="form-control-label">Data</label>
                                            <input required class="form-control" type="date" id="party_day" name="party_day">
                                            <x-input-error :messages="$errors->get('party_day')" class="mt-2" />
                                        </div>
    
                                        <div class="form-group col-md-4">
                                            <label for="schedule_id" class="form-control-label">Horários disponíveis</label>
                                            <select required name="schedule_id" id="schedule_id" class="form-control">
                                                <option value="invalid" selected disabled>Selecione um horário disponível</option>
                                            </select>
                                            <x-input-error :messages="$errors->get('schedule_id')" class="mt-2" />
                                        </div>
    
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button class="btn btn-primary w-100" type="button" id="verify-disponibility">Buscar Disponibilidade</button>
                                        </div>
                                    </div>
                                    <x-input-helper :value="'A data escolhida aqui pode ou não estar disponivel, basta clicar no botão ao lado para confirmar!'" />
                                </div>
                                <div>
                                    <p>Preço: </p>
                                </div>
                                <div>
                                    <button class="btn btn-primary button_submit_booking" type="submit" disabled id="button_pre_booking">Fazer Pré Reserva</button>
                                    <button class="btn btn-primary button_submit_booking" type="submit" disabled id="button_pre_booking_visit">Fazer Pré Reserva e Agendar Visita</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
    
<script>
    let can_create_booking = false;
    const submit_buttons = document.querySelectorAll(".button_submit_booking")
    const form = document.querySelector("#form")

    const SITEURL = "{{ url('/') }}";
    const csrf = document.querySelector('meta[name="csrf-token"]').content
    const food = new Swiper('.food_slider', {
            // Optional parameters
            direction: 'horizontal',
            loop: true,
            slidesPerView: 3,
            spaceBetween: 10,

            // If we need pagination
            pagination: {
                el: '.swiper-pagination',
            },

            // Navigation arrows
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
        const decoration = new Swiper('.decoration_slider', {
            // Optional parameters
            direction: 'horizontal',
            loop: true,
            slidesPerView: 3,
            spaceBetween: 10,

            // If we need pagination
            pagination: {
                el: '.swiper-pagination',
            },

            // Navigation arrows
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });

        async function get_food(food_slug) {
            const csrf = document.querySelector('meta[name="csrf-token"]').content
            const data = await axios.get(SITEURL + '/api/{{ $buffet->slug }}/food/' + food_slug, {
                headers: {
                    'X-CSRF-TOKEN': csrf
                }
            })

            return data.data;
        }

        const see_food_details = document.querySelectorAll(".see-food-details-button")
        see_food_details.forEach((button)=>{
            button.addEventListener('click', async (e)=>{
                e.preventDefault()

                const btn_slug = button.id.split('button-food-')[1]

                const food = await get_food(btn_slug)
                console.log(food)
                const data = {
                    title: food.data.name_food,
                    content: `
                        <p><b>Por apenas R$ ${food.data.price}</b></p>
                        <p><b>Descrição do pacote:</b></p>
                        <p><b>Comidas:</b></p>
                        ${food.data.food_description}
                        <p><b>Bebidas:</b></p>
                        ${food.data.beverages_description}
                        <p><b>Fotos:</b></p>
                        ${food.data.photos.map(photo=>{
                            return `
                            <img style="width: 400px; height: 300px" src="{{asset('storage/foods/${photo.file_path}')}}">
                            `
                        }).join('<br>')}
                        `
                }
                html(data)
            })
        })

        async function get_decoration(decoration_slug) {
            const csrf = document.querySelector('meta[name="csrf-token"]').content
            const data = await axios.get(SITEURL + '/api/{{ $buffet->slug }}/decoration/' + decoration_slug, {
                headers: {
                    'X-CSRF-TOKEN': csrf
                }
            })

            return data.data;
        }

        const see_decoration_details = document.querySelectorAll(".see-decoration-details-button")
        see_decoration_details.forEach((button)=>{
            button.addEventListener('click', async (e)=>{
                e.preventDefault()

                const btn_slug = button.id.split('button-decoration-')[1]
                
                const decoration = await get_decoration(btn_slug)
                const data = {
                    title: decoration.data.main_theme,
                    content: `
                        <p><b>Por apenas R$ ${decoration.data.price}</b></p>
                        <p><b>Descrição do pacote:</b></p>
                        <p><b>Comidas:</b></p>
                        ${decoration.data.description}
                        ${decoration.data.photos.map(photo=>{
                            return `
                            <img style="width: 400px; height: 300px" src="{{asset('storage/decorations/${photo.file_path}')}}">
                            `
                        }).join('<br>')}
                    `
                    // <img class="w-full" src="{{asset('storage/packages/${food.photo_1}')}}">
                    // <img class="w-full" src="{{asset('storage/packages/${pk.photo_2}')}}">
                    // <img class="w-full" src="{{asset('storage/packages/${pk.photo_3}')}}">
                }
                html(data)
            })
        })


        const party_day = document.querySelector("#party_day")
        const party_time = document.querySelector("#schedule_id")

    document.addEventListener('DOMContentLoaded', (event) => {
        const SITEURL = "{{ url('/') }}";
        const csrf = document.querySelector('meta[name="csrf-token"]').content
    
        party_day.addEventListener('change', async function() {
            const agora = new Date();
            const escolhida = new Date(this.value + 'T00:00:00');
            while (party_time.options.length > 1) {
                party_time.remove(1); // Remova a segunda opção em diante (índice 1)
            }
            const min_days = {{ $min_days }}
            agora.setDate(agora.getDate() + min_days);
            if (escolhida < agora) {
                const data = agora.toISOString().split('T')[0]
                this.value = data;
                error(`Você só pode marcar festas após ${min_days} dias contados a partir da data de hoje (${data}).`)
            }

            const dates = await getDates(this.value)

            printDates(dates)

        });
        form.addEventListener('submit', async function(e) {
            e.preventDefault()
            const userConfirmed = await confirm(`Deseja cadastrar uma festa ?`)

            try {
                const party_day = document.querySelector("#party_day")
                const party_time = document.querySelector("#schedule_id")

                await verifyDisponibility(party_day.value, party_time.value, false)

                if (userConfirmed) {
                    this.submit();
                } else {
                    error("Ocorreu um erro!")
                }
            }catch(e) {
                error("Horario indisponivel!")
            }

            
        })
    })

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


    async function verifyDisponibility(day, time, print) {
    if(print) {
        basic("Verificando disponibilidade...")
    }
    setTimeout(async () => {
        if(!day || !time) {
        error("Valores incompletos.")
        return;
    }
    try {
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const data = await axios.get(SITEURL + '/api/{{$buffet->slug}}/booking/schedule/' + day + '/' + time + '/disponibility', {
            headers: {
                'X-CSRF-TOKEN': csrf
            }
        });

        const html_data = {
            title: "Horario da festa",
            content: `
                <h3>Horario Disponivel!</h3>
            `
        };

        html(html_data);

        submit_buttons.forEach(button => {
            button.disabled = false;
        });
    } catch(e) {
        console.log(e);
        submit_buttons.forEach(button => {
            button.disabled = true;
        });

        const html_data = {
            title: "Horario da festa",
            content: `
                <h4>Infelizmente o horário escolhido se encontra indisponível!</h4>
                <h5>Aqui se encontra uma lista de possíveis outros horários para escolha:</h5>
                ${e.response.data.alternativas.map(date => {
                    const horarioFinal = formatarHora(date.horario.comeco, date.horario.duracao);
                    return `<button class="btn button_indisponible_date" value="${date.data}//${date.horario.id}">${date.data} das ${date.horario.comeco} até ${horarioFinal}</button>`;
                }).join('')}
                <button class="btn btn-primary">Contatar um funcionário para garantir o melhor horario</button>
            `
        };

        html(html_data);

        // Chame a função para adicionar os event listeners após a inserção do HTML
        const buttons = document.querySelectorAll(".button_indisponible_date");
        console.log(buttons); // Verifica se os botões estão sendo encontrados
        buttons.forEach(button => {
            button.addEventListener('click', async e => {
                e.preventDefault();
                console.log(button); // Verifica se o clique está sendo capturado
                const party_day = document.querySelector("#party_day");
                const party_time = document.querySelector("#schedule_id");

                const [date, time] = button.value.split("//");

                const dates = await getDates(date)

                printDates(dates)

                party_day.value = date;
                party_time.value = time;

                submit_buttons.forEach(button => {
                    button.disabled = false;
                });

                close_modal()
            });
        });
    }
    }, 1500);
    
}   

    const verifyDisponibilityButton = document.querySelector("#verify-disponibility")
    verifyDisponibilityButton.addEventListener("click", async e => {
        e.preventDefault();

        const party_day = document.querySelector("#party_day")
        const party_time = document.querySelector("#schedule_id")

        await verifyDisponibility(party_day.value, party_time.value, true)

    })

    const formatarHora = (horario, duracao) => {
        // Divide o horário em horas, minutos e segundos
        const [hora, minuto, segundo] = horario.split(':').map(Number);
        
        // Converte tudo para minutos e soma a duração
        const totalSegundos = (hora * 3600) + (minuto * 60) + segundo + (duracao * 60);
        
        // Calcula as novas horas, minutos e segundos
        const novasHoras = Math.floor(totalSegundos / 3600);
        const novosMinutos = Math.floor((totalSegundos % 3600) / 60);
        const novosSegundos = totalSegundos % 60;
        
        // Formata os valores para 'HH:MM:SS'
        return `${novasHoras.toString().padStart(2, '0')}:${novosMinutos.toString().padStart(2, '0')}:${novosSegundos.toString().padStart(2, '0')}`;
    };


</script>
@endsection