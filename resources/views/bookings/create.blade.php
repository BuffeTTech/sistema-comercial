@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Reserva', 'subtitle'=>'Criar Reserva'])
    <link rel="stylesheet"href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <style>
        /* Ocultar todos os passos exceto o atual */
        .step-content {
          display: none;
        }
        .step-content.active {
          display: block;
        }
        .progress {
            height: 30px; /* Ajuste o valor conforme necessário */
        }
        .input-radio input[type=radio] {
            display: none;
        }

        .input-radio input[type=radio]:checked~label {
            background-color: #e0ba31;
            color: white;
        }
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
                        <div class="progress mb-4 h-30 dont-copy">
                            <div class="progress-bar" id="progressBar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">Etapa 1 de 4</div>
                        </div>
                        <div class="table-responsive px-4">
                            <form id="form" method="POST" action="{{ route('booking.store', ['buffet'=>$buffet->slug]) }}">
                                @csrf
                                <!-- Passo 1 -->
                                <div class="step-content active" id="step-1">
                                    <h5>Informações da Festa - Dados do aniversariante</h5>
                                    <div class="form-group">
                                        <label for="name_birthdayperson" class="form-control-label">Nome*</label>
                                        <input  class="form-control" type="text" placeholder="Guilherme" id="name_birthdayperson" name="name_birthdayperson" value="{{ old('name_birthdayperson') }}" required>
                                        <x-input-error :messages="$errors->get('name_birthdayperson')" class="mt-2" />
                                    </div>
                                    <div class="form-group">
                                        <label for="years_birthdayperson" class="form-control-label">Idade*</label>
                                        <input required class="form-control" type="number" placeholder="20" id="years_birthdayperson" name="years_birthdayperson" value="{{ old('years_birthdayperson') }}">
                                        <x-input-error :messages="$errors->get('years_birthdayperson')" class="mt-2" />
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="birthday_date" class="form-control-label">Data de Nascimento*</label>
                                        <input required class="form-control" type="date" id="birthday_date" name="birthday_date" value="{{ old('birthday_date') }}">
                                        <x-input-error :messages="$errors->get('birthday_date')" class="mt-2" />
                                    </div>
                                  <button type="button" class="btn btn-primary" onclick="nextStep()">Próximo</button>
                                </div>
                          
                                <!-- Passo 2 -->
                                <div class="step-content" id="step-2">
                                    <h5>Informações da Festa - Detalhes do evento</h5>
                                    <div style="position: relative;">
                                        <label for="food_id" class="form-control-label">Pacote de comidas*</label>
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
                                            <div class="swiper-pagination"></div>
            
                                            <div class="swiper-button-prev"></div>
                                            <div class="swiper-button-next"></div>
            
                                        </div>
                                        <x-input-error :messages="$errors->get('food_id')" class="mt-2" />
                                    </div>
                                    <div class="form-group">
                                        <label for="additional_foods_observations" class="form-control-label">Observações Sobre as comidas</label>
                                        <textarea class="form-control textarea-container" id="additional_foods_observations" rows="3" name="additional_foods_observations" placeholder="Descrição das comidas do pacote">{{ old('additional_foods_observations') }}</textarea>
                                        <x-input-error :messages="$errors->get('additional_foods_observations')" class="mt-2" />
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="dietary_restrictions" name="dietary_restrictions"
                                                @if (old('dietary_restrictions')) checked @endif>
                                            <label class="form-check-label" for="dietary_restrictions">Há restrição alimentar?</label>
                                        </div>
                                        <x-input-error :messages="$errors->get('dietary_restrictions')" class="mt-2" />
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="external_food" name="external_food"
                                                @if (old('external_food')) checked @endif>
                                            <label class="form-check-label" for="external_food">Irá levar comida externa?</label>
                                        </div>
                                        <x-input-error :messages="$errors->get('external_food')" class="mt-2" />
                                    </div>
                                    <div style="position: relative">
                                        <label for="decoration_id" class="form-control-label">Pacote de decoração*</label>
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
                                                            R$: <span class="font-bold text-xl">{{number_format((float) $decoration['price'], 2)}}</span>
                                                        </span>
                                                        <button id='button-decoration-{{$decoration['slug']}}'class="see-decoration-details-button btn btn-secondary block">Ver detalhes</button>
                                                    </label>
                                                </div>
                                                @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if($configuration->external_decoration)
                                        <div class="col-md-4">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="external_decoration" name="external_decoration"
                                                    @if (old('external_decoration')) checked @endif>
                                                <label class="form-check-label" for="external_decoration">Irá levar decoração externa?</label>
                                            </div>
                                            <x-input-error :messages="$errors->get('external_decoration')" class="mt-2" />
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <label for="num_guests" class="form-control-label">Número de convidados*</label>
                                        <input required class="form-control" type="number" placeholder="50" id="num_guests" name="num_guests" value="{{ old('num_guests') }}">
                                        <x-input-error :messages="$errors->get('num_guests')" class="mt-2" />
                                    </div>
                                    {{-- <div class="form-group">
                                        <label for="daytime_preference" class="form-control-label">Prefêrencia de Horário*</label>
                                        <select class="form-select" multiple aria-label="multiple select example" name="daytime_preference" id="daytime_preference" required>
                                            @foreach(App\Enums\DayTimePreference::array() as $value => $name)
                                                <option value="{{ $name }}" 
                                                    {{ (is_array(old('daytime_preference')) && in_array($value, old('daytime_preference'))) ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('daytime_preference')" class="mt-2" />
                                    </div> --}}
                                    <div class="form-group">
                                        <label for="price_preview" class="form-control-label">Prévia do Preço</label>
                                        <input class="form-control" type="text" id="price_preview" name="price_preview" value="{{ old('price_preview') ?? 0}}" readonly>
                                        <x-input-error :messages="$errors->get('price_preview')" class="mt-2" />
                                    </div>
                                        
                                    <button type="button" class="btn btn-secondary" onclick="previousStep()">Anterior</button>
                                    <button type="button" class="btn btn-primary" onclick="nextStep()">Próximo</button>
                                </div>
                          
                                <!-- Passo 3 -->
                                <div class="step-content" id="step-3">
                                  <h5>Horário</h5>
                                  <p>Com base nas informações fornecidas, temos as seguintes opções de horário:</p>
                                  <div class="row row-cols-6 row-cols-md-4 row-cols-sm-2 " id="dates-wrapper"></div> <!-- printar as datas -->
                                  <div>
                                      <p>Nenhuma data convém?</p>
                                      <button id="find_date_button" class="btn btn-secondary">Buscar data específica</button>
                                      @if($configuration->buffet_whatsapp)
                                        <a class="btn btn-secondary" href="{{ $configuration->buffet_whatsapp }}?text=Gostaria%20de%20agendar%20uma%20festa%20e%20nenhum%20horario%20me%20convem" target="_blank">Falar com atendente</a>
                                      @endif
                                  </div>
                                    <div class="form-group">
                                        <label for="final_notes" class="form-control-label">Observações finais</label>
                                        <textarea class="form-control textarea-container" id="final_notes" rows="3" name="final_notes" placeholder="Descrição das comidas do pacote">{{ old('final_notes') }}</textarea>
                                        <x-input-error :messages="$errors->get('final_notes')" class="mt-2" />
                                    </div>
                                  <div>
                                        <button type="button" class="btn btn-secondary" onclick="previousStep()">Anterior</button>
                                        <button type="button" class="btn btn-primary" onclick="nextStep()">Próximo</button>
                                  </div>
                                </div>
                                <div class="step-content" id="step-4">
                                  <h5>Confirmação da festa</h5>
                                  <p>Por favor, revise suas informações antes de enviar.</p>
                                  <div id="all-content">

                                  </div>
                                    <button type="button" class="btn btn-secondary" onclick="previousStep()">Anterior</button>
                                    <button type="submit" class="btn btn-success">Fazer pré reserva</button>
                                    <input type="hidden" name="schedule_visit" value="false" id="schedule_visit">
                                    @if($configuration->buffet_whatsapp)
                                        <button type="submit" class="btn btn-success" id="schedule_visit_button">Fazer pré reserva e agendar visita</button>
                                    @endif
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

        const button_schedule = document.querySelector("#schedule_visit_button")
        if(button_schedule) {
            const newTabUrl = "{{ $configuration->buffet_whatsapp }}text=Pre%20agendei%20%minha%20festa%20e%20quero%20agendar%20uma%20visita!";

            button_schedule.addEventListener('click', e => {
                e.preventDefault()
                const schedule_visit = document.querySelector("#schedule_visit")
                schedule_visit.value = true;
                window.open(newTabUrl, '_blank');
                form.submit()
            })
        }

        // constantes
        const SITEURL = "{{ url('/') }}";
        const csrf = document.querySelector('meta[name="csrf-token"]').content
        const form = document.querySelector('#form')
        const price_preview = document.querySelector('#price_preview')
        const food_id = document.querySelectorAll('input[name=food_id]')
        const decoration_id = document.querySelectorAll('input[name=decoration_id]')
        const num_guests = document.querySelector('input[name=num_guests]')
        const find_date_button = document.querySelector("#find_date_button")
        const daytime_preference = document.querySelector("#daytime_preference")
        const birthday_date = document.querySelector("#birthday_date")
        const dates_wrapper = document.querySelector("#dates-wrapper")
        
        const daysOfWeek = ["Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sábado"];
        const months = [
                "Jan", "Fev", "Mar", "Abr", "Mai", "Jun",
                "Jul", "Ago", "Set", "Out", "Nov", "Dez"
            ];
        let decorationSelected = null;
        let foodSelected = null;

        const prices = {
            food: 0,
            decoration: 0
        }
    </script>
    <script>
        let currentStep = 1;

        const steps = ["Informações", "Detalhes", "Agendamento", "Confirmação"]
        document.getElementById("progressBar").innerText = steps[0];
    
        function showStep(step) {
          document.querySelectorAll(".step-content").forEach((content, index) => {
            content.classList.remove("active");
          });
          document.getElementById(`step-${step}`).classList.add("active");
    
          // Atualiza a barra de progresso
          const progress = (step / 4) * 100;
          document.getElementById("progressBar").style.width = `${progress}%`;
          document.getElementById("progressBar").innerText = steps[step - 1];
        //   document.getElementById("progressBar").innerText = `Etapa ${step} de 3`;
        }

        async function nextStep() {
            // Seleciona todos os campos de entrada obrigatórios na etapa atual
            const currentStepElement = document.getElementById(`step-${currentStep}`);
            const inputs = currentStepElement.querySelectorAll('input[required], select[required]');
            let allValid = true;


            inputs.forEach(input => {
                // Verificação para campos de texto, email, número, etc.
                if (input.type === "text" || input.type === "number" || input.type === "email") {
                    if (input.value.trim() === "") {
                        allValid = false;
                        input.classList.add("is-invalid"); // Adiciona uma classe para indicar erro
                    } else {
                        input.classList.remove("is-invalid");
                    }
                }
                // Verificação para campos de rádio
                else if (input.type === "radio") {
                    const radioGroup = currentStepElement.querySelectorAll(`input[name="${input.name}"]`);
                    const isRadioChecked = Array.from(radioGroup).some(radio => radio.checked);
                    if (!isRadioChecked) {
                        allValid = false;
                        radioGroup.forEach(radio => radio.classList.add("is-invalid"));
                    } else {
                        radioGroup.forEach(radio => radio.classList.remove("is-invalid"));
                    }
                }
                // Verificação para campos <select multiple>
                else if (input.tagName === "SELECT" && input.multiple) {
                    const selectedOptions = Array.from(input.selectedOptions);
                    if (selectedOptions.length === 0) {
                        allValid = false;
                        input.classList.add("is-invalid");
                    } else {
                        input.classList.remove("is-invalid");
                    }
                }
            });

            // Se todos os campos estiverem válidos, passa para o próximo passo
            if (allValid) {
                if(currentStep + 1 == 3) {
                    await generateSchedule()
                } else if(currentStep + 1 == 4) {
                    await printResume();
                }
                currentStep++;
                showStep(currentStep)
            } else {
                error("Por favor, preencha todos os campos obrigatórios antes de continuar.");
            }
        }

    
        function previousStep() {
          currentStep--;
          showStep(currentStep);
        }
    
        form.addEventListener('submit', async function(e) {
            e.preventDefault()
            const userConfirmed = await confirm(`Deseja cadastrar uma festa ?`)

            try {
                if (userConfirmed) {
                    this.submit();
                } else {
                    error("Ocorreu um erro!")
                }
            }catch(e) {
                error("Horario indisponivel!")
            }  
        })

        // document.getElementById("multiStepForm").addEventListener("submit", function (event) {
        //   event.preventDefault();
        //   alert("Formulário enviado com sucesso!");
        // });
    </script>
    <script>
        async function printResume() {
            const container_content = document.querySelector("#all-content")
            
            const name_birthdayperson = document.querySelector("#name_birthdayperson").value
            const years_birthdayperson = document.querySelector("#years_birthdayperson").value
            const birthday_date = document.querySelector("#birthday_date").value
            const food_id = document.querySelector("input[name=food_id]:checked") //
            const additional_foods_observations = document.querySelector("#additional_foods_observations").value
            const dietary_restrictions = document.querySelector("#dietary_restrictions") //
            const external_food = document.querySelector("#external_food") //
            const decoration_id = document.querySelector("input[name=decoration_id]:checked") //
            const external_decoration = document.querySelector("#external_decoration") //
            const num_guests = document.querySelector("#num_guests").value
            const price_preview = document.querySelector("#price_preview").value
            const final_notes = document.querySelector("#final_notes").value

            const food = await get_food(food_id.value)
            const decoration = await get_decoration(decoration_id.value)

            console.log(additional_foods_observations)

            container_content.innerHTML = `
                Nome do aniversariante: ${name_birthdayperson}<br>
                Idade: ${years_birthdayperson}<br>
                Data de nascimento: ${birthday_date}<br>
                Pacote de comida: ${food.data.name_food}<br>
                Observações a respeito da comida: ${additional_foods_observations != "" ? additional_foods_observations : "Nada"}<br>
                Restrições alimentares: ${dietary_restrictions.checked ? "Sim" : "Não"}<br>
                Comidas externas: ${external_food.checked ? "Sim" : "Não"}<br>
                Pacote de decoração: ${decoration.data.main_theme}<br>
                Numero de convidados: ${num_guests}<br>
                Preço: ${price_preview}<br>
                ${external_decoration != null ?  `Decoração externa: ${external_decoration.checked ? "Sim" : "Não"}<br>` : ""}
                Observações finais: ${final_notes != "" ? additional_foods_observations : "Nada"}<br>
            `                

        }
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
        function printPrice(element) {
            element.value = `R$ ${prices.food + prices.decoration}`;
        }
        async function handleFoodChange(event) {
            const selectedValue = event.target.value;
            const { data } = await get_food(selectedValue)

            foodSelected = data;
            
            prices.food = data.price * num_guests.value;
            printPrice(price_preview)
        }

        food_id.forEach(async radio => {
            radio.addEventListener('change', handleFoodChange);
        });
        async function handleDecorationChange(event) {
            const selectedValue = event.target.value;
            const { data } = await get_decoration(selectedValue)

            decorationSelected = data;
            
            prices.decoration = data.price;
            printPrice(price_preview)
        }

        decoration_id.forEach(async radio => {
            radio.addEventListener('change', handleDecorationChange);
        });

        num_guests.addEventListener('change', e=>{
            prices.decoration = decorationSelected != null ? decorationSelected.price : 0;
            prices.food = foodSelected != null ? foodSelected.price * num_guests.value : 0;
            printPrice(price_preview)
        })

        find_date_button.addEventListener('click', async (e)=>{
            e.preventDefault()

            const data = {
                title: "Selecionar data",
                content: `
                    <p>Buscar por datas</p>
                    <input type="date" id="date_popup"/>
                    <button id="select_date_popup">Selecionar opçoes</button>   

                    <div id="hours_popup"></div>
                `
            }
            const a = htmlFunction(data, async ()=>{
                console.log("b")
                const button = document.querySelector("#select_date_popup")
                const input = document.querySelector("#date_popup")
                const div = document.querySelector("#hours_popup")
                button.addEventListener('click', async e=>{
                    e.preventDefault()

                    if(!input.value) {
                        alert("Você precisa selecionar uma data.")
                        return;
                    }
                    let dates = null;
                    try {
                        const data = await get_specific_hours(input.value)
                        dates = data.dates
                    } catch (error) {
                        div.innerHTML = error.response.data.message
                        return                   
                    }

                    const dateFormat = new Date(`${input.value}T00:00:00Z`);
                    const dateDay = dateFormat.getUTCDate();
                    const dateMonth = months[dateFormat.getUTCMonth()];
                    const dateYear = dateFormat.getUTCFullYear();

                    div.innerHTML = dates.map((date, index)=>{
                        const dayOfWeek = daysOfWeek[dateFormat.getUTCDay()];
                        const horarioFinal = formatarHora(date.start_time, date.duration);
                        const startTimeFormat = date.start_time.slice(0, 5);
                        const endTimeFormat = horarioFinal.slice(0, 5);

                        return `
                            <button class="button_date_popup" id="button_date_popup-${index}" value="${input.value};;${date.id}"><bold>${dayOfWeek}<bold><br> Dia ${dateDay} de ${dateMonth} de ${dateYear} <br> ${startTimeFormat}h até ${endTimeFormat}h</button>
                        `
                    }).join("<br>")

                    document.querySelectorAll(".button_date_popup").forEach(button=>{
                        button.addEventListener('click', (e)=>{
                            e.preventDefault()
                            dates_wrapper.innerHTML += `
                                <div>
                                    <input type="radio" class="btn-check" name="party_day" id="party_day_extra_${button.id}" autocomplete="off" value="${button.value}">
                                    <label for="party_day_extra_${button.id}"class="btn btn-primary">${button.innerHTML}</label>
                                </div>
                            `
                            a.close()
                        })
                    })
                })
            })
            // .then(() => {
            //     console.log("Modal foi aberto e .then() executado");
            //     const popupContainer = Swal.getHtmlContainer();
            //     if (popupContainer) {
            //         console.log("dasd");
            //     }

            // }).catch((error) => {
            //     console.error("Erro na execução da Promise:", error);
            // });

        })


        async function get_specific_hours(day) {
            const data = await axios.get(SITEURL + '/api/{{$buffet->slug}}/booking/schedule/' + day + '/disponibility', {
                headers: {
                    'X-CSRF-TOKEN': csrf
                }
            });
            return data.data
        }

        async function get_schedule_by_birthday_date(birthday_date, daytime_preferences) {
            const csrf = document.querySelector('meta[name="csrf-token"]').content
            const data = await axios.get(SITEURL + '/api/{{ $buffet->slug }}/booking/schedule/' + birthday_date+'/birthday?daytime_preference='+daytime_preferences.join(','), {
                headers: {
                    'X-CSRF-TOKEN': csrf
                }
            })

            return data.data;
        }

        async function generateSchedule() {
            // const daytime_preferences = Array.from(daytime_preference.selectedOptions).map(option => option.value);
            const daytime_preferences = [];
            let dates = null;
            try {
                const data = await get_schedule_by_birthday_date(birthday_date.value, daytime_preferences)
                dates = data.dates
            } catch (error) {
                dates_wrapper.innerHTML = error.response.data.message
                return                   
            }
            
            dates_wrapper.innerHTML = ''
            
            dates.forEach((day, index1)=>{
                const dateFormat = new Date(`${day.day}T00:00:00Z`);
                const dateDay = dateFormat.getUTCDate();
                const dateMonth = months[dateFormat.getUTCMonth()];
                const dateYear = dateFormat.getUTCFullYear();


                const dayOfWeek = daysOfWeek[dateFormat.getUTCDay()];
                 day.schedules.forEach((schedule, index2)=>{
                    const horarioFinal = formatarHora(schedule.start_time, schedule.duration);
                    const startTimeFormat = schedule.start_time.slice(0, 5);
                    const endTimeFormat = horarioFinal.slice(0, 5);
                    dates_wrapper.innerHTML += `
                        <div class="col">
                            <input type="radio" class="btn-check" name="party_day" id="party_day_${index1}_${index2}" autocomplete="off" value="${day.day};;${schedule.id}">
                            <label for="party_day_${index1}_${index2}" class="btn btn-primary"><bold>${dayOfWeek}<bold><br> Dia ${dateDay} de ${dateMonth} de ${dateYear} <br> ${startTimeFormat}h até ${endTimeFormat}h</label>
                        </div>
                    `
                })
            })
        }

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