@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Funcionários'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between">
                        <h6>Listagem dos Funcionários</h6>
                        <a href="{{ route('employee.create', ['buffet'=>$buffet->slug]) }}" class="btn btn-outline-primary btn-sm fs-6 btn-tooltip" title="Adicionar Funcionário">Adicionar Funcionário</a>                                        
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Nome</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Email</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Cargo</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($employees) === 0)
                                    <tr>
                                        <td colspan="3" class="p-3 text-sm text-center">Nenhum funcionário encontrado</td>
                                    </tr>
                                    @else
                                        @foreach($employees as $value)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            <h6 class="text-sm mb-0">{{$value['name']}}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            <p class="text-sm mb-0">{{$value['email']}}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                            <p class="text-sm mb-0">{{$value->roles[0]->name ?? ""}}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <x-status.user_status :status="$value['status']" />
                                                </td>
                                                <td class="align-middle">
                                                    @can('view employee')
                                                        <a href="{{ route('employee.show', ['employee'=>$value['hashed_id'], 'buffet'=>$buffet->slug]) }}" title="Visualizar '{{$value->name}}'" class="btn btn-outline-primary btn-sm fs-6">👁️</a>
                                                    @endcan
                                                    @can('update employee')
                                                        @if($value['status'] === App\Enums\UserStatus::ACTIVE->name)
                                                            <a href="{{ route('employee.edit', ['employee'=>$value['hashed_id'], 'buffet'=>$buffet->slug]) }}" title="Editar '{{$value->name}}'" class="btn btn-outline-primary btn-sm fs-6">✏️</a>
                                                        @endif
                                                    @endcan
                                                    @can('change employee status')
                                                        @if($value['status'] !== App\Enums\UserStatus::UNACTIVE->name)
                                                            <form action="{{ route('employee.destroy', ['employee'=>$value['hashed_id'], 'buffet'=>$buffet->slug]) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('delete')
                                                                <button type="submit" class="btn btn-outline-primary btn-sm fs-6" title="Desativar Funcionário" >❌</button>                                        
                                                            </form>
                                                        @else
                                                            <form action="{{ route('employee.change_status', ['employee'=>$value['hashed_id'], 'buffet'=>$buffet->slug]) }}" method="post" class="d-inline">
                                                                @csrf
                                                                @method('patch')
                                                                <input type="hidden" name="status" value="{{ App\Enums\UserStatus::ACTIVE->name }}">
                                                                <button type="submit" title="Ativar Funcionário" class="btn btn-outline-primary btn-sm fs-6">✅</button>
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
                                {{ $employees->links('components.pagination') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
@endsection
