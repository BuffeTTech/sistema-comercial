@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'funcionários', 'subtitle'=>"Visualizar funcionário"])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Funcionários</h6>
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="px-4">
                            <h3>{{ $employee->name }}</h3>
                            <p class="text-md mb-0"><strong>Email:</strong> {{ $employee->email }}</p>
                            <p class="text-md mb-0"><strong>Cargo:</strong> {{ $employee->roles[0]->name ?? "" }}</p>
                            <p class="text-md mb-0"><strong>Telefone:</strong> {{ $employee->phone1}}</p>


                            <p class="text-lg mb-0"><strong>Status:</strong> <x-status.user_status :status="$employee['status']" /></p>
                            <br>

                            
                        @can('update employee')
                            <a href="{{ route('employee.edit', ['buffet'=>$buffet->slug, 'employee'=>$employee['hashed_id']]) }}" title="Editar dados" class="btn btn-outline-primary btn-sm fs-6">Editar</a>
                        @endcan
                        @can('change employee status')
                            @if($employee['status'] !== App\Enums\UserStatus::UNACTIVE->name)
                                <form action="{{ route('employee.destroy', ['buffet'=>$buffet->slug, 'employee'=>$employee['hashed_id']]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-outline-primary btn-sm fs-6" title="Desativar Funcionário">❌ Desligar Funcionário</button>                                        
                                </form>
                            @else
                                <form action="{{ route('employee.change_status', ['employee'=>$employee['hashed_id'], 'buffet'=>$buffet->slug]) }}" method="post" class="d-inline">
                                    @csrf
                                    @method('patch')
                                    <input type="hidden" name="status" employee="{{App\Enums\UserStatus::ACTIVE->name }}">
                                    <button type="submit" title="Ativar Funcionário" class="btn btn-outline-primary btn-sm fs-6">✅ Re-Admitir Funcionário</button>
                                </form>
                            @endif  
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
@endsection
