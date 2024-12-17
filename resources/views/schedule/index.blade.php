@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Recomendações'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex flex-wrap justify-content-between">
                        <h6>Horário de festas</h6>
                        <a href="{{ route('schedule.create', ['buffet'=>$buffet->slug]) }}" class="btn btn-outline-primary btn-sm fs-6 btn-tooltip" title="Criar Horário">Criar Horário</a>                                        
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Dia da semana</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Hora de início</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Duração</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Fim</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Início de bloqueio</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Final de bloqueio</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($schedules) === 0)
                                    <tr>
                                        <td colspan="3" class="p-3 text-sm text-center">Nenhuma horário encontrada</td>
                                    </tr>
                                    @else
                                        @foreach($schedules as $value)
                                    
                                            <tr>
                                                <td class="text-center">
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            <h6 class="mb-0 text-sm">{{ App\Enums\DayWeek::getEnumByName($value['day_week']) }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            <p class="text-sm mb-0">{{$value['start_time']}}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            <p class="text-sm mb-0">{{$value['duration']}} minutos</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            <p class="text-sm mb-0">{{ \Carbon\Carbon::parse($value->start_time)->addMinutes($value->duration)->format('H:i:s') }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            @if($value['start_block']=== null)
                                                                <p class="text-sm mb-0">Não existe</p>
                                                                @else
                                                                <p class="text-sm mb-0">{{$value['start_block'] }}</p>
                                                                <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center"></td>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            @if($value['start_block']=== null)
                                                                <p class="text-sm mb-0">Não existe</p>
                                                                @else
                                                                <p class="text-sm mb-0">{{$value['end_block'] }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <x-status.schedule_status :status="$value['status']" />
                                                </td>
                                                <td class="align-middle">
                                                    @can('update schedule')
                                                        @if($value['status'] === App\Enums\ScheduleStatus::ACTIVE->name)
                                                            <a href="{{ route('schedule.edit', ['schedule'=>$value['hashed_id'], 'buffet'=>$buffet->slug]) }}" title="Editar '{{ App\Enums\DayWeek::getEnumByName($value['day_week']) }}" class="btn btn-outline-primary btn-sm fs-6">✏️</a>
                                                        @endif
                                                    @endcan
                                                    @can('change schedule status')
                                                        @if($value['status'] !== App\Enums\ScheduleStatus::UNACTIVE->name)
                                                            <form action="{{ route('schedule.destroy', ['schedule'=>$value['hashed_id'], 'buffet'=>$buffet->slug]) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('delete')
                                                                <button type="submit" class="btn btn-outline-primary btn-sm fs-6" title="Desativar horário" >❌</button>                                        
                                                            </form>
                                                        @else
                                                            <form action="{{ route('schedule.change_status', ['schedule'=>$value['hashed_id'], 'buffet'=>$buffet->slug]) }}" method="post" class="d-inline">
                                                                @csrf
                                                                @method('patch')
                                                                <input type="hidden" name="status" value="{{App\Enums\ScheduleStatus::ACTIVE->name }}">
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
                                {{ $schedules->links('components.pagination') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
@endsection
