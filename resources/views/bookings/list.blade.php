@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Reservas'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between">
                        <h6>Listagem de todas as reservas {{$format == 'pendent' ? 'pendentes' : ''}}</h6>
                        @if($format == "pendent")
                            @can('list booking')
                                <a href="?format=all" class="btn btn-outline-primary btn-sm fs-6 btn-tooltip" title="Criar decoração">Ver todas as reservas</a> 
                            @endcan
                        @else
                            @can('view pendent bookings')
                                <a href="?format=pendent" class="btn btn-outline-primary btn-sm fs-6 btn-tooltip" title="Criar decoração">Ver reservas pendentes</a> 
                            @endcan
                        @endif
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Nome Aniversariante</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Máx. Convidados</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Comida</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Decoração</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Dia da Festa</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Inicio</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Fim</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($bookings) === 0)
                                    <tr>
                                        <td colspan="8" class="p-3 text-sm text-center">Nenhuma reserva encontrada</td>
                                    </tr>
                                    @else
                                        @foreach($bookings as $booking)
                                    
                                            <tr>
                                                <td class="text-center">
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            <h6 class="mb-0 text-sm">{{$booking['name_birthdayperson'] }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            <p class="text-sm mb-0">{{$booking['num_guests']}}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            <p class="text-sm mb-0">{{$booking->food['slug']}}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            <p class="text-sm mb-0">{{$booking->decoration['slug']}}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            <p class="text-sm mb-0">{{ date('d/m/Y',strtotime($booking->party_day))  }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            <p class="text-sm mb-0">{{date("H:i", strtotime($booking->schedule['start_time']))}}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            <p class="text-sm mb-0">{{ date("H:i", strtotime(\Carbon\Carbon::parse($booking->schedule['start_time'])->addMinutes($booking->schedule['duration']))) }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <x-status.booking_status :status="$booking['status']" />
                                                </td>
                                                <td class="text-center">
                                                    @can('change booking status')
                                                        @if($booking->status === App\Enums\BookingStatus::PENDENT->name && $format == 'pendent')
                                                            <form action="{{ route('booking.change_status', ['buffet'=>$buffet->slug, 'booking'=>$booking->hashed_id]) }}" method="post" class="d-inline">
                                                                @csrf
                                                                @method('patch')
                                                                <input type="hidden" name="status" value="{{App\Enums\BookingStatus::APPROVED->name}}">
                                                                <button type="submit" title="Aprovar festa '{{$booking->name_birthdayperson}}'" class="btn btn-outline-primary btn-sm fs-6">✅</button>
                                                            </form>
                                                            <form action="{{ route('booking.change_status', ['buffet'=>$buffet->slug, 'booking'=>$booking->hashed_id]) }}"  method="post" class="d-inline">
                                                                @csrf
                                                                @method('patch')
                                                                <input type="hidden" name="status" value="{{App\Enums\BookingStatus::REJECTED->name}}">
                                                                <button type="submit" title="Negar festa '{{$booking->name_birthdayperson}}'" class="btn btn-outline-primary btn-sm fs-6">❌</button>
                                                            </form>
                                                        @endif
                                                    @endcan
                                                    @can('view booking')
                                                        <a href="{{ route('booking.show', ['booking'=>$booking->hashed_id, 'buffet'=>$buffet->slug]) }}" title="Visualizar '{{$booking->name_birthdayperson}}'" class="btn btn-outline-primary btn-sm fs-6">👁️</a>
                                                    @endcan
                                                    @can('edit booking')
                                                        @if($booking['status'] === App\Enums\BookingStatus::APPROVED->name || $booking['status'] === App\Enums\BookingStatus::PENDENT->name)
                                                            <a href="{{ route('booking.edit', ['booking'=>$booking->hashed_id, 'buffet'=>$buffet->slug]) }}" title="Editar '{{$booking->name_birthdayperson}}'" class="btn btn-outline-primary btn-sm fs-6">✏️</a>
                                                        @endif
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            <div class="px-2">
                                {{ $bookings->links('components.pagination') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
@endsection
