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
            background-color: #FB6340;
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
                        <div class="progress mb-4 h-30">
                            <div class="progress-bar" id="progressBar" role="progressbar" style="width: 33%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100">Etapa 1 de 3</div>
                        </div>
                        <div class="table-responsive px-4">
                            <form id="form" method="POST" action="{{ route('booking.store', ['buffet'=>$buffet->slug]) }}">
                                @csrf
                                <!-- Passo 1 -->
                                <div class="step-content active" id="step-1">
                                    <h5>Informações da Festa - Dados do aniversariante</h5>
                                    <div class="form-group">
                                        <label for="name_birthdayperson" class="form-control-label">Nome</label>
                                        <input  class="form-control" type="text" placeholder="Guilherme" id="name_birthdayperson" name="name_birthdayperson" value="{{ old('name_birthdayperson') }}" required>
                                        <x-input-error :messages="$errors->get('name_birthdayperson')" class="mt-2" />
                                    </div>
                                    <div class="form-group">
                                        <label for="years_birthdayperson" class="form-control-label">Idade</label>
                                        <input required class="form-control" type="number" placeholder="20" id="years_birthdayperson" name="years_birthdayperson" value="{{ old('years_birthdayperson') }}">
                                        <x-input-error :messages="$errors->get('years_birthdayperson')" class="mt-2" />
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="party_day" class="form-control-label">Data de Nascimento</label>
                                        <input required class="form-control" type="date" id="party_day" name="party_day">
                                        <x-input-error :messages="$errors->get('party_day')" class="mt-2" />
                                    </div>
                                  <button type="button" class="btn btn-primary" onclick="nextStep()">Próximo</button>
                                </div>
                          
                                <!-- Passo 2 -->
                                <div class="step-content" id="step-2">
                                    <h5>Informações da Festa - Detalhes do evento</h5>
                                    <div style="position: relative;">
                                        <label for="food_id" class="form-control-label">Pacote de comidas</label>
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
                                            <input class="form-check-input" type="checkbox" id="dietary_restricions" name="dietary_restricions"
                                                @if (old('dietary_restricions')) checked @endif>
                                            <label class="form-check-label" for="dietary_restricions">Há restrição alimentar?</label>
                                        </div>
                                        <x-input-error :messages="$errors->get('dietary_restricions')" class="mt-2" />
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
                                        <label for="decoration_id" class="form-control-label">Pacote de decoração</label>
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
                                        <label for="num_guests" class="form-control-label">Número de convidados</label>
                                        <input required class="form-control" type="number" placeholder="50" id="num_guests" name="num_guests" value="{{ old('num_guests') }}">
                                        <x-input-error :messages="$errors->get('num_guests')" class="mt-2" />
                                    </div>
                                    <div class="form-group">
                                        <label for="daytime_preference" class="form-control-label">Prefêrencia de Horário</label>
                                        <select class="form-select" multiple aria-label="multiple select example">
                                            <option selected>Open this select menu</option>
                                            @foreach(App\Enums\DayTimePreference::array() as $value => $name)
                                                <option value="{{ $name }}" 
                                                    {{ (is_array(old('daytime_preference')) && in_array($value, old('daytime_preference'))) ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('daytime_preference')" class="mt-2" />
                                    </div>
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
                                  <h5>Confirmação da festa</h5>
                                  <p>Com base nas informações fornecidas, temos as seguintes opções de horário:</p>
                                  <div>
                                    <button>a</button>
                                    <button>b</button>
                                    <button>c</button>
                                  </div>
                                  <p>Nenhuma data convém?</p>
                                  <button id="find_date_button" class="btn btn-secondary">Buscar data específica</button>
                                  @if($configuration->buffet_whatsapp)
                                    <a class="btn btn-secondary" href="{{ $configuration->buffet_whatsapp }}?text=Gostaria%20de%20agendar%20uma%20festa%20e%20nenhum%20horario%20me%20convem" target="_blank">Falar com atendente</a>
                                  @endif
                                  <p>Por favor, revise suas informações antes de enviar.</p>
                                  <!-- Aqui você pode listar os dados inseridos para revisão -->
                                  <button type="button" class="btn btn-secondary" onclick="previousStep()">Anterior</button>
                                  <button type="submit" class="btn btn-success">Fazer pré reserva</button>
                                  <button type="submit" class="btn btn-success">Fazer pré reserva e agendar visita</button>
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
        // constantes
        const SITEURL = "{{ url('/') }}";
        const csrf = document.querySelector('meta[name="csrf-token"]').content
        const form = document.querySelector('#form')
        const price_preview = document.querySelector('#price_preview')
        const food_id = document.querySelectorAll('input[name=food_id]')
        const decoration_id = document.querySelectorAll('input[name=decoration_id]')
        const num_guests = document.querySelector('input[name=num_guests]')
        const find_date_button = document.querySelector("#find_date_button")

        let decorationSelected = null;
        let foodSelected = null;

        const prices = {
            food: 0,
            decoration: 0
        }
    </script>
    <script>
        let currentStep = 1;

        const steps = ["Informações da festa", "Detalhes do evento", "Confirmação e Agendamento"]
        document.getElementById("progressBar").innerText = steps[0];
    
        function showStep(step) {
          document.querySelectorAll(".step-content").forEach((content, index) => {
            content.classList.remove("active");
          });
          document.getElementById(`step-${step}`).classList.add("active");
    
          // Atualiza a barra de progresso
          const progress = (step / 3) * 100;
          document.getElementById("progressBar").style.width = `${progress}%`;
          document.getElementById("progressBar").innerText = steps[step - 1];
        //   document.getElementById("progressBar").innerText = `Etapa ${step} de 3`;
        }
    
        function nextStep() {
            // Seleciona todos os campos de entrada obrigatórios na etapa atual
            const currentStepContent = document.getElementById(`step-${currentStep}`);
            const inputs = currentStepContent.querySelectorAll("input[required]");

            // Verifica se todos os campos obrigatórios estão preenchidos
            let allFilled = true;
            inputs.forEach(input => {
                if (!input.value.trim()) { // trim() remove espaços em branco
                allFilled = false;
                input.classList.add("is-invalid"); // Adiciona uma classe para indicar erro
                } else {
                input.classList.remove("is-invalid"); // Remove o erro se preenchido
                }
            });

            // Se todos os campos estiverem preenchidos, passa para o próximo passo
            if (allFilled) {
                currentStep++;
                showStep(currentStep);
            } else {
                alert("Por favor, preencha todos os campos obrigatórios antes de prosseguir.");
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
                `
            }
            html(data)
        })

    </script>
@endsection