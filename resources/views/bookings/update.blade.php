<x-app-layout>
    <link rel="stylesheet"href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <style>
        .input-radio input[type=radio] {
            display: none;
        }

        .input-radio input[type=radio]:checked~label {
            background-color: #facc15;
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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold mb-4">Agendar reserva</h1>
                    <form method="POST" action="{{ route('booking.update', ['buffet'=>$buffet->slug, 'booking'=>$booking->hashed_id]) }}">
                        @csrf
                        @method('put')

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        <div>
                            <x-input-label for="name_birthdayperson" :value="__('Nome do aniversariante')" class="dark:text-slate-800"/>
                            <x-text-input id="name_birthdayperson" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="text" name="name_birthdayperson" :value="old('name_birthdayperson') ?? $booking->name_birthdayperson" required autofocus placeholder="Nome do aniversariante" />
                            <x-input-error :messages="$errors->get('name_birthdayperson')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="years_birthdayperson" :value="__('Idade do aniversariante')" class="dark:text-slate-800"/>
                            <x-text-input id="years_birthdayperson" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="number" min="0" step="1" name="years_birthdayperson" :value="old('years_birthdayperson') ?? $booking->years_birthdayperson" required autofocus placeholder="Idade do aniversariante" />
                            <x-input-error :messages="$errors->get('years_birthdayperson')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="num_guests" :value="__('Quantidade de convidados')" class="dark:text-slate-800"/>
                            <x-text-input id="num_guests" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="number" min="0" step="1" name="num_guests" :value="old('num_guests') ?? $booking->num_guests" required autofocus placeholder="Quantidade de convidados" />
                            <x-input-error :messages="$errors->get('num_guests')" class="mt-2" />
                        </div>
                        <div style="position: relative">
                            <x-input-label :value="__('Pacote de comidas')" class="dark:text-slate-800"/>
                            {{-- <x-text-input id="food_id" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="date" name="food_id" :value="old('food_id')" required autofocus placeholder="Dia da festa" /> --}}
                            <div class="food_slider">
                                <!-- Additional required wrapper -->
                                <div class="swiper-wrapper">
                                    <!-- Slides -->
                                    @if(count($foods) === 0)
                                    <h1>Nenhum pacote de comida encontrado!</h1>
                                    @else
                                    @foreach($foods as $key => $food)

                                    <div class="swiper-slide input-radio p-4 max-w-xl rounded overflow-hidden shadow-lg">
                                        <input 
                                            {{ $key === 0 ? "required" : "" }} 
                                            {{ $booking->food_id == $food['id'] ? "checked" : ""}}
                                            type="radio" 
                                            name="food_id" 
                                            id="food-{{$food['slug']}}" 
                                            value="{{$food['slug']}}" 
                                            class="px-8 py-8" >
                                        <label for="food-{{$food['slug']}}" class="px-6 py-4 bg-amber-100 block">
                                            <span class="font-bold block text-lg">
                                                {{$food['name_food']}}
                                            </span>
                                            <span class="block">
                                                R$: <span class="font-bold text-xl">{{number_format((float) $food['price'], 2)}}</span> p/ pessoa
                                            </span>
                                            <button id='button-food-{{$food['slug']}}'class="see-food-details-button bg-amber-400 hover:bg-amber-500 text-black font-bold py-2 px-4 rounded
                                                inline-flex items-center px-3 py-2 border border-transparent text-sm leading-">Ver detalhes</button>
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

                                    <div class="swiper-slide input-radio p-4 max-w-xl rounded overflow-hidden shadow-lg">
                                        <input 
                                            {{ $key === 0 ? "required" : "" }}
                                            {{ $booking->decoration_id == $decoration['id'] ? "checked" : ""}}
                                            type="radio"
                                            name="decoration_id"
                                            id="decoration-{{$decoration['slug']}}" 
                                            value="{{$decoration['slug']}}" 
                                            class="px-8 py-8" >
                                        <label for="decoration-{{$decoration['slug']}}" class="px-6 py-4 bg-amber-100 block">
                                            <span class="font-bold block text-lg">
                                                {{$decoration['main_theme']}}
                                            </span>
                                            <span class="block">
                                                R$: <span class="font-bold text-xl">{{number_format((float) $decoration['price'], 2)}}</span> p/ pessoa
                                            </span>
                                            <button id='button-decoration-{{$decoration['slug']}}'class="see-decoration-details-button bg-amber-400 hover:bg-amber-500 text-black font-bold py-2 px-4 rounded
                                                inline-flex items-center px-3 py-2 border border-transparent text-sm leading-">Ver detalhes</button>
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
                            <x-input-label for="party_day" :value="__('Dia da festa')" class="dark:text-slate-800"/>
                            <x-text-input id="party_day" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="date" name="party_day" :value="old('party_day') ?? $booking->party_day" required autofocus placeholder="Dia da festa" />
                            <x-input-error :messages="$errors->get('party_day')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="schedule_id" :value="__('Horário da festa')" class="dark:text-slate-800"/>
                            <select name="schedule_id" id="schedule_id" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" required autofocus placeholder="Horário da festa">
                                <option value="invalid" selected disabled>Selecione um horario disponível</option>
                            </select>
                            <x-input-error :messages="$errors->get('schedule_id')" class="mt-2" />
                            <span class="text-sm text-red-600 dark:text-red-400 space-y-1" id="schedule-error"></span>
                        </div>

                        <div>
                            <x-input-label for="price" :value="__('Preço')" class="dark:text-slate-800"/>
                            <x-text-input id="price" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="text" name="price" :value="0" autofocus placeholder="Preço" disabled/>
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Agendar festa') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // variaveis
        const SITEURL = "{{ url('/') }}";
        const csrf = document.querySelector('meta[name="csrf-token"]').content
        
        const party_day = document.querySelector("#party_day")
        const party_time = document.querySelector("#schedule_id")
        const schedule = document.querySelector("#schedule_id")
        const num_guests = document.querySelector("#num_guests")
        const price = document.querySelector("#price")

        const foods = document.querySelectorAll("[name=food_id]")
        const decorations = document.querySelectorAll("[name=decoration_id]")

        let food_selected = {}
        let decoration_selected = {}

        // sliders
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

        foods.forEach((food) => {
            food.addEventListener('change', async (e) => {
                const invited = qtd_invited.value ?? 0
                const food_local = await get_food(e.target.value)
                food_selected = food_local;

                // price.innerHTML = invited * food_local.price
            })
        })
        
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
                const data = {
                    title: food.data.name_food,
                    content: `
                        <p><b>Por apenas R$ ${food.data.price}</b></p>
                        <br>
                        <p><b>Descrição do pacote:</b></p>
                        <br>
                        <p><b>Comidas:</b></p>
                        ${food.data.food_description}
                        <br><br>
                        <p><b>Bebidas:</b></p>
                        ${food.data.beverages_description}
                        <br><br>
                        ${food.data.photos.map(photo=>{
                            return `
                            <img class="w-full" src="{{asset('storage/foods/${photo.file_path}')}}">
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



        async function execute() {
            // const food = document.querySelector('input[name=food_id]:checked')
            // const decoration = document.querySelector('input[name=decoration_id]:checked')
            // const invited = num_guests.value ?? 0
            // let price_local = 0;
            // if (food) {
            //     const food_local = await get_food(food.value)
            //     food_selected = food_local;
            //     price_local += invited * food_local.price
            // }
            // if(decoration) {
            //     const decoration_local = await get_decoration(decoration.value)
            //     decoration_selected = decoration_local;
            //     price_local += invited * decoration_local.price
            // }
            // price.innerHTML = price_local;

            if (party_day.value) {
                const dates = await getDates(party_day.value)

                printDates(dates)
            }
        }
        
        execute()



        // schedules
       
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
                return;
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

    </script>
</x-app-layout>