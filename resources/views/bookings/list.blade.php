@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Recomendações'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between">
                        <h6>Reservas Confirmadas</h6>
                        <a href="{{ route('booking.create', ['buffet'=>$buffet->slug]) }}" class="btn btn-outline-primary btn-sm fs-6 btn-tooltip" title="Adicionar Reserva">Adicionar reserva</a>                                        
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
                                        <td colspan="3" class="p-3 text-sm text-center">Nenhuma reserva encontrada</td>
                                    </tr>
                                    @else
                                        @foreach($bookings as $value)
                                    
                                            <tr>
                                                <td class="text-center">
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            <h6 class="mb-0 text-sm">{{$value['name_birthdayperson'] }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            <p class="text-sm mb-0">{{$value['num_guests']}}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            <p class="text-sm mb-0">{{$value->food['slug']}}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            <p class="text-sm mb-0">{{$value->decoration['slug']}}</p>
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
                                                    <x-status.booking_status :status="$value['status']" />
                                                </td>
                                                <td class="align-middle">
                                                    @can('update schedule')
                                                        @if($value['status'] === App\Enums\Bookingtatus::ACTIVE->name)
                                                            <a href="{{ route('schedule.edit', ['schedule'=>$value['hashed_id'], 'buffet'=>$buffet->slug]) }}" title="Editar '{{ App\Enums\DayWeek::getEnumByName($value['day_week']) }}" class="btn btn-outline-primary btn-sm fs-6">✏️</a>
                                                        @endif
                                                    @endcan
                                                    @can('change schedule status')
                                                        @if($value['status'] !== App\Enums\Bookingtatus::UNACTIVE->name)
                                                            <form action="{{ route('schedule.destroy', ['schedule'=>$value['hashed_id'], 'buffet'=>$buffet->slug]) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('delete')
                                                                <button type="submit" class="btn btn-outline-primary btn-sm fs-6" title="Desativar horário" >❌</button>                                        
                                                            </form>
                                                        @else
                                                            <form action="{{ route('schedule.change_status', ['schedule'=>$value['hashed_id'], 'buffet'=>$buffet->slug]) }}" method="post" class="d-inline">
                                                                @csrf
                                                                @method('patch')
                                                                <input type="hidden" name="status" value="{{App\Enums\Bookingtatus::ACTIVE->name }}">
                                                                <button type="submit" title="Ativar horário" class="btn btn-outline-primary btn-sm fs-6">✅</button>
                                                            </form>
                                                        @endif    
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            <div class="px-2">
                                {{ $booking->links('components.pagination') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
@endsection
