@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Reserva', 'subtitle'=>'Modo Festa'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Modo Festa</h6>
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-4 pt-0 pb-2">
                        @if($booking)
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <h4>{{ $booking->name_birthdayperson }}</h4>
                            @can('change booking status')
                                @if($booking['status'] === App\Enums\BookingStatus::APPROVED->name)
                                <form action="{{ route('booking.change_status', ['buffet' => $buffet->slug, 'booking' => $booking->hashed_id]) }}" method="POST">
                                    @csrf
                                    @method('patch')
                                    <input type="hidden" name="status" value="{{App\Enums\BookingStatus::FINISHED->name}}">
                                    <button type="submit" class="btn btn-outline-danger btn-sm fs-6" title="Cancelar Festa">❌ Finalizar Festa</button>                                        
                                </form>
                                @endif  
                            @endcan
                        </div>
                            @php
                                $total_guests_present = $guest_counter['present'] + $guest_counter['extras'];
                                $total_guests_stipulated = $guest_counter['unblocked'] - $guest_counter['extras'];
                            @endphp
                            <div class="card-group">
                                <div class="card">
                                    <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1">
                                        <h5>Informações da festa</h5>
                                        <ul class="list-group list-group-flush mt-2">
                                            <li class="list-group-item">Nome do aniversariante: {{ $booking->name_birthdayperson }}</li>
                                            <li class="list-group-item">Idade:  {{$booking->years_birthdayperson}}</li>
                                            <li class="list-group-item">Buffet: {{$booking->buffet->trading_name}}</li>
                                            <li class="list-group-item">Organizador: {{$booking->user->name}}</li>
                                            <li class="list-group-item">Data: {{$booking->party_day}}</li>
                                            <li class="list-group-item">Preço: R$ {{ $booking->price_food * $booking->num_guests + $booking->price_decoration * $booking->num_guests + $booking->price_schedule  * $booking->num_guests }}</li>
                                            <li class="list-group-item">{{$total_guests_present}} de {{$total_guests_stipulated}} (convidados pagos {{ $booking->num_guests}})</li>
                                            <li class="list-group-item">
                                                <div class="accordion-2">
                                                    <div class="row">
                                                        <div class="accordion" id="accordionRental">
                                                            <div class="accordion-item mb-2">
                                                                <h5 class="accordion-header" id="headingOne"><strong>
                                                                    <button class="accordion-button border-bottom collapsed ps-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                                                        Pacote de comida: {{ $booking->food->name_food }}
                                                                    <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                                                    <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                                                    </button>
                                                                </strong></h5>
                                                                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionRental" style="">
                                                                    <ul>
                                                                        <li>Slug: {{ $booking->food->slug }}</li>
                                                                        <li>Status: <x-status.food_status :status="$booking->food['status']" /></li>
                                                                        <li><span class="badge bg-gradient-primary"><strong>Preço:</strong> R$ {{ $booking->food->price }}</span></li>
                                                                        <li>Descrição das comidas:<br>
                                                                            {!! $booking->food->food_description !!}
                                                                        </li>
                                                                        <li>Descrição das bebidas:<br>
                                                                            {!! $booking->food->beverages_description !!}
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="accordion-item mb-2">
                                                                <h5 class="accordion-header" id="headingTwo"><strong>
                                                                    <button class="accordion-button border-bottom collapsed ps-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                                        Pacote de decoração: {{ $booking->decoration->main_theme }}
                                                                    <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                                                    <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                                                    </button>
                                                                </strong></h5>
                                                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionRental" style="">
                                                                    <ul>
                                                                        <li>Slug: {{ $booking->decoration->slug }}</li>
                                                                        <li>Status: <x-status.decoration_status :status="$booking->decoration['status']" /></li>
                                                                        <li><span class="badge bg-gradient-primary"><strong>Preço:</strong> R$ {{ $booking->decoration->price }}</span></li>
                                                                        <li>Descrição das comidas:<br>
                                                                            {!! $booking->decoration->description !!}
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                   <!-- Card body -->
                                   <div class="card-body">
                                   </div>
                                </div>
                                @can('create guest')
                                    <div class="card">
                                        <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1">
                                            <h5>Convidados Extras</h5>
                                        </div>
                                    
                                        <div class="card-body pt-2">
                                            <form method="POST" action="{{ route('guest.store', ['buffet'=>$buffet->slug, 'booking'=>$booking->hashed_id]) }}" enctype="multipart/form-data" id="form">
                                                @csrf
                                                <x-input-error :messages="$errors->get('error')" class="mt-2" />
                                                <div id="form-rows">
                                                    <input type="hidden" name="rows[0][status]" id= 'status' value={{App\Enums\GuestStatus::EXTRA->name}}>
                                                    <div class="form-group">
                                                        <label for="name0" class="form-control-label">Nome</label>
                                                        <input class="form-control" type="text" placeholder="Nome" id="name0" name="rows[0][name]" value="{{ old('rows[0][name]') }}">
                                                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="document0" class="form-control-label">CPF</label>
                                                        <input class="form-control document" type="text" placeholder="CPF" id="document0" name="rows[0][document]" value="{{ old('rows[0][document]') }}">
                                                        <span class="text-sm text-red-600 dark:text-red-400 space-y-1 document-error" id="document-error0"></span>
                                                        <x-input-error :messages="$errors->get('document')" class="mt-2" />
                                                    </div>
            
                                                    <div class="form-group">
                                                        <label for="age0" class="form-control-label">Idade</label>
                                                        <input class="form-control" type="number" value="{{old('rows[0][age]')}}" id="age0" placeholder="Idade" name="rows[0][age]">
                                                        <x-input-error :messages="$errors->get('age')" class="mt-2" />
                                                    </div>
                                                </div>
                                                <button class="btn btn-primary" type="submit">Cadastrar Convidado</button>
                                            </form>
                                        </div>
                                    </div>
                                @endcan
                            </div>
                            @can('list booking guests')
                                <div class="card">
                                    <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1">
                                        <h5>Convidados Confirmados</h5>
                                    </div>
                                
                                    <div class="card-body pt-2">
                                        @if($guest_counter === 0 || count($guests) == 0)
                                            <h5>Esta festa não possui convidados!</h5>
                                        @else
                                        <div class="table-responsive p-0">
                                            <table class="table align-items-center mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Nome do Convidado</th>
                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">CPF</th>
                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Idade</th>
                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                                                        <th class="text-secondary opacity-7"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(count($guests) === 0)
                                                    <tr>
                                                        <td colspan="7" class="p-3 text-sm text-center">Nenhum convidado confirmado</td>
                                                    </tr>
                                                    @else
                                                        @foreach($guests as $guest)
                                                            <tr>
                                                                <td class="text-center">
                                                                    <div class="d-flex px-2 py-1">
                                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                                            <h6 class="mb-0 text-sm">{{ $guest->name }}</h6>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="d-flex px-2 py-1">
                                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                                            <p class="text-sm mb-0">{{$guest->document}}</p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="d-flex px-2 py-1">
                                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                                            <p class="text-sm mb-0">{{$guest->age}}</p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="d-flex px-2 py-1">
                                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                                            <p class="text-sm mb-0"><x-status.guest_status :status="$guest['status']" /></p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="d-flex px-2 py-1">
                                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                                            <div class="text-sm mb-0">
                                                                                @if($booking->status == App\Enums\BookingStatus::APPROVED->name)
                                                                                    @if($guest->status == App\Enums\GuestStatus::PENDENT->name)
                                                                                        <form action="{{ route('guest.change_status', ['buffet' => $buffet->slug, 'guest' => $guest['hashed_id'], 'booking' => $booking->hashed_id]) }}" method="POST" class="d-inline">
                                                                                            @csrf
                                                                                            @method('PATCH')
                                        
                                                                                            <input type="hidden" name="status" value="{{App\Enums\GuestStatus::CONFIRMED->name}}">
                                                                                            <button type="submit" class="btn btn-success" title="Confirmar '{{$guest->name}}'">✅</button>
                                                                                        </form>
                                                                                        <form action="{{ route('guest.change_status', ['buffet' => $buffet->slug, 'guest' => $guest['hashed_id'], 'booking' => $booking->hashed_id]) }}" method="POST" class="d-inline">
                                                                                            @csrf
                                                                                            @method('PATCH')
                                        
                                                                                            <input type="hidden" name="status" value="{{App\Enums\GuestStatus::BLOCKED->name}}">
                                                                                            <button type="submit" class="btn" title="Bloquear '{{$guest->name}}'">❌</button>
                                                                                        </form>
                                                                                    @elseif($guest->status == App\Enums\GuestStatus::CONFIRMED->name)
                                                                                        <form action="{{ route('guest.change_status', ['buffet' => $buffet->slug, 'guest' => $guest['hashed_id'], 'booking' => $booking->hashed_id]) }}" method="POST" class="d-inline">
                                                                                            @csrf
                                                                                            @method('PATCH')
                                        
                                                                                            @if($total_guests_present >= $booking->num_guests)
                                                                                                <input type="hidden" name="status" value="{{App\Enums\GuestStatus::EXTRA->name}}">
                                                                                            @else
                                                                                                <input type="hidden" name="status" value="{{App\Enums\GuestStatus::PRESENT->name}}">
                                                                                            @endif
                                                                                            <button type="submit" class="btn" title="Confirmar Presença '{{$guest->name}}'">✅</button>
                                                                                        </form>  
                                                                                        <form action="{{ route('guest.change_status', ['buffet' => $buffet->slug, 'guest' => $guest['hashed_id'], 'booking' => $booking->hashed_id]) }}" method="POST" class="d-inline">
                                                                                            @csrf
                                                                                            @method('PATCH')
                                        
                                                                                            <input type="hidden" name="status" value="{{App\Enums\GuestStatus::ABSENT->name}}">
                                                                                            <button type="submit" class="btn" title="Confirmar Ausência '{{$guest->name}}'">❌</button>
                                                                                        </form>  
                                                                                    @endif
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
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
                            @endcan
                        @else
                            <h3>Não existe festa em andamento!</h3>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
    <script>
        const doc = document.querySelector("#document0")
        const doc_error = document.querySelector("#document-error0")
        const form = document.querySelector("#form")

        form.addEventListener('submit', async function (e) {
            // e.preventDefault()
            // const cpfs = document.querySelectorAll('.document')

            // let erro = false
            // cpfs.forEach(cpf => {
            //     const cpf_valid = validarCPF(cpf.value)
            //     if(!cpf_valid) {
            //         error("O CPF é invalido")
            //         erro = true
            //         return;
            //     }
            // });
            // if(erro) return
            // this.submit();
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
@endsection