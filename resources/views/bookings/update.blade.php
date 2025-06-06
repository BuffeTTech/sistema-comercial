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
                            <form method="POST" action="{{ route('booking.update', ['buffet'=>$buffet->slug,'booking'=>$booking->hashed_id]) }}">
                                @csrf
                                @method('put')
                                <div class="form-group">
                                    <label for="name_birthdayperson" class="form-control-label">Nome do Aniversariante</label>
                                    <input  class="form-control" type="text" placeholder="Guilherme" id="name_birthdayperson" name="name_birthdayperson" value="{{ old('name_birthdayperson') ?? $booking->name_birthdayperson }}">
                                    <x-input-error :messages="$errors->get('name_birthdayperson')" class="mt-2" />
                                </div>

                                <div class="form-group">
                                    <label for="years_birthdayperson" class="form-control-label">Idade do Aniverasariante</label>
                                    <input required class="form-control" type="years_birthdayperson" placeholder="20" id="years_birthdayperson" name="years_birthdayperson" value="{{ old('years_birthdayperson') ?? $booking->years_birthdayperson}}">
                                    <x-input-error :messages="$errors->get('years_birthdayperson')" class="mt-2" />
                                </div>

                                <div class="form-group">
                                    <label for="num_guests" class="form-control-label">Número de convidados</label>
                                    <input required class="form-control" type="num_guests" placeholder="50" id="num_guests" name="num_guests" value="{{ old('num_guests') ?? $booking->num_guests }}">
                                    <x-input-error :messages="$errors->get('num_guests')" class="mt-2" />
                                </div>

                                <div style="position: relative">
                                    <x-input-label :value="__('Pacote de comidas')"/>
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
                                                <input {{ $key === 0 ? "required" : "" }} {{ $booking->food_id == $food['id'] ? "checked" : ""}} type="radio" name="food_id" id="food-{{$food['slug']}}" value="{{$food['slug']}}" {{ old('food_id') == $food->slug ? 'checked="true"' : ''}}>
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
                                    <x-input-label :value="__('Pacote de decoração')" class="dark:text-slate-800"/>
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
                                                <input {{ $key === 0 ? "required" : "" }} {{ $booking->decoration_id == $decoration['id'] ? "checked" : ""}}    type="radio" name="decoration_id" id="decoration-{{$decoration['slug']}}" value="{{$decoration['slug']}}" class="px-8 py-8" {{ old('decoration_id') == $decoration->slug ? 'checked="true"' : ''}}>
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

                                <div class="form-group">
                                    <label for="party_day" class="form-control-label">Data</label>
                                    <input required class="form-control" type="date" id="party_day" name="party_day" value="{{ $booking->party_day }}">
                                    <x-input-error :messages="$errors->get('party_day')" class="mt-2" />
                                </div>

                                <div class="form-group">
                                    <label for="schedule_id" class="form-control-label">Horários disponíveis</label>
                                    <select required name="schedule_id" id="schedule_id" class="form-control" >
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
    
<script>
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
                        <br><br>
                        <p><b>Fotos:</b></p>
                        ${food.data.photos.map(photo=>{
                            return `
                            <img class="w-full" src="{{asset('storage/foods/${photo.file_path}')}}">
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
                        <br>
                        <p><b>Descrição do pacote:</b></p>
                        <br>
                        <p><b>Comidas:</b></p>
                        ${decoration.data.description}
                        <br><br>
                        ${decoration.data.photos.map(photo=>{
                            return `
                            <img class="w-full" src="{{asset('storage/decorations/${photo.file_path}')}}">
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

    document.addEventListener('DOMContentLoaded', async (event) => {
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
            const data = await axios.get(SITEURL + '/api/{{$buffet->slug}}/booking/schedule/' + day + '?booking={{ $booking->hashed_id }}', {
                headers: {
                    'X-CSRF-TOKEN': csrf
                }
            })
            console.log(data)

            return data.data;
        }

        const original_schedule = "{{ $booking->schedule_id }}"

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

            console.log(original_schedule, options)
            for (let i = 0; i < options.length; i++) {
                const option = document.createElement("option");
                option.text = options[i].msg;
                option.value = options[i].value
                party_time.appendChild(option);
                if(options[i].value == Number(original_schedule)) {
                    option.selected = true
                }

            }
        }

        if (party_day.value) {
            const dates = await getDates(party_day.value)

            printDates(dates)
        }


    })

</script>
@endsection