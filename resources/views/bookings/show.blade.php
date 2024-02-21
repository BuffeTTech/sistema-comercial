@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Reservas', 'subtitle'=>"Visualizar Reserva"])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Visualizar Reserva</h6>
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="px-4">
                            <h3>{{ $booking->name_birthdayperson }}</h3>
                            <p class="text-lg mb-0"><strong>Status:</strong> <x-status.booking_status :status="$booking['status']" /></p>
                            <br> 
                            <!-- Info aniversario && Info Pacotes  -->
                            <div class="card-group">
                                <div class="card">
                                  <div class="card-body pt-2">
                                    <a href="javascript:;" class="card-title h5 d-block text-darker">
                                      Informações Aniversário 
                                    </a>
                                    <p class="card-description mb-4">
                                        <p class="text-md mb-0"><strong>Idade:</strong> {{ $booking->years_birthdayperson }}</p>
                                        <p class="text-md mb-0"><strong>Número de convidados:</strong> {{ $booking->num_guests}}</p>
                                        <p class="text-md mb-0"><strong>Dia da Festa:</strong> {{ $booking->party_day}}</p>
                                        <p class="text-md mb-0"><strong>Horário da Festa:</strong> {{ $booking->schedule['start_time'] }}</p>
                                    </p>
                                  </div>
                                </div>
                                <div class="card">
                                  <div class="card-body pt-2">
                                    <a href="javascript:;" class="card-title h5 d-block text-darker">
                                      Informações Pacotes 
                                    </a>
                                    <p class="card-description mb-4">
                                        <p class="text-md mb-0"><strong>Pacote de Comida:</strong> {{ $booking->food->name_food}}</p>
                                        <p class="text-md mb-0"><strong>Preço do Pacote:</strong> {{ $booking->price_food}}</p>
                                        <p class="text-md mb-0"><strong>Pacote de Decoração:</strong> {{ $booking->decoration->main_theme}}</p>
                                        <p class="text-md mb-0"><strong>Preço da Decoração:</strong> {{ $booking->price_decoration}}</p>
                                    </p>
                                  </div>
                                </div>
                            </div>
                            <br>
                            <!-- Preco final  -->
                            <p class="text-md mb-0"><strong>Preço Final:</strong> {{ $booking->price_food * $booking->num_guests + $booking->price_decoration * $booking->num_guests + $booking->price_schedule  * $booking->num_guests }}</p>

                            @if($booking->status == App\Enums\BookingStatus::FINISHED->name || $booking->status == App\Enums\BookingStatus::CLOSED->name)
                                @php
                                    $total_guests_present = $guest_counter['present'] + $guest_counter['extras'];
                                    $total_guests_stipulated = $guest_counter['unblocked'] - $guest_counter['extras'];
                                @endphp
                                <h4><strong>Convidados presentes/estipulados:</strong>{{$total_guests_present}}/{{$total_guests_stipulated}}</h4><br>
                            
                            @endif

                            <!-- Lista de convidados -->

                            <div class="accordion-1">
                                <div class="row">
                                    <div class="accordion" id="accordionRental">
                                        <div class="accordion-item mb-2">
                                            <h5 class="accordion-header" id="headingOne"><strong>
                                                <button class="accordion-button border-bottom font-weight-bold collapsed ps-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                                    Lista de convidados 
                                                <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                                <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                                </button>
                                            </strong></h5>
                                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionRental" style="">
                                                <div class="card-body px-0 pt-0 pb-2">
                                                    @can('create guest')
                                                        @if($booking->status ==  \App\Enums\BookingStatus::APPROVED->name)
                                                            <div>
                                                                <div class="input-group mb-3">
                                                                    <input type="text" readonly class="form-control" id="copy-input" value="{{ route('guest.invite', ['buffet'=>$buffet->slug, 'booking'=>$booking->hashed_id]) }}">
                                                                    <button class="btn btn-outline-primary mb-0" type="button" id="button-copy-input"><i class="ni ni-ungroup"></i></button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endcan
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
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>   
                        <!-- Recomendacoes -->
                        @can('view booking recommendations')
                            @if($booking->status ==  \App\Enums\BookingStatus::APPROVED->name)
                            <div class="accordion-2">
                                <div class="row">
                                    <div class="accordion" id="accordionRental">
                                        <div class="accordion-item mb-2">
                                            <h5 class="accordion-header" id="headingTwo"><strong>
                                                <button class="accordion-button border-bottom font-weight-bold collapsed ps-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                    Recomendações
                                                <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                                <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                                </button>
                                            </strong></h5>
                                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionRental" style="">
                                                @foreach ($recommendations as $recommendation)
                                                    <div class="accordion-body text-sm opacity-8">
                                                        <ul>{!!$recommendation['content']!!}</ul>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endcan
                        <br>
                        @can('update booking')
                            <a href="{{ route('booking.edit', ['buffet'=>$buffet->slug, 'booking'=>$booking['hashed_id']]) }}" title="Editar dados" class="btn btn-outline-primary btn-sm fs-6">Editar</a>
                        @endcan
                        @can('change booking status')
                            @if($booking['status'] === App\Enums\BookingStatus::APPROVED->name || $booking['status'] === App\Enums\BookingStatus::PENDENT->name)
                                <form action="{{ route('booking.change_status', ['buffet' => $buffet->slug, 'booking' => $booking->hashed_id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('patch')
                                    <input type="hidden" name="status" value="{{App\Enums\BookingStatus::CANCELED->name}}">
                                    <button type="submit" class="btn btn-outline-primary btn-sm fs-6" title="Desativar Funcionário">❌ Cancelar Reserva</button>                                        
                                </form>
                            @endif  
                        @endcan

                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
    <script>
        const btn = document.querySelector('#button-copy-input')
        if(btn) {
            btn.addEventListener('click', (e) => {
                copiarTexto()
            })
        }
        async function copiarTexto() {
            let textoCopiado = document.getElementById("copy-input").value;

            try {
                await navigator.clipboard.writeText(textoCopiado);
                await basic('Link copiado para a área de transferência');
            } catch (error) {
                console.error('Falha ao copiar texto: ', error);
            }
        }
    </script>
@endsection
