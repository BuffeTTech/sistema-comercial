@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Usuários'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex flex-wrap justify-content-between">
                        <h6>Listagem dos Usuários</h6>
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
                                            @can('change buffet user role')
                                                @if($total < $configurations['max_employees'])
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Atualizar Cargo</th>
                                                @endif
                                            @endcan
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($users) === 0)
                                    <tr>
                                        <td colspan="4" class="p-3 text-sm text-center">Nenhum usuário encontrado</td>
                                    </tr>
                                    @else
                                        @foreach($users as $value)
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
                                                @can('change buffet user role')
                                                    @if($total < $configurations['max_employees'])
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                                    <form method="POST" action="{{ route('user.change_role', ['buffet'=>$buffet->slug, 'user'=>$value->hashed_id]) }}">
                                                                        @csrf
                                                                        @method('PATCH')

                                                                        <div class="form-group">
                                                                            <select name="role" id="role" class="form-control" onchange="this.form.submit()">
                                                                                @php
                                                                                    $slug = $buffet_subscription->subscription->slug;
                                                                                @endphp
                                                                                @foreach($roles as $role)
                                                                                    <option 
                                                                                        {{ $value->roles[0]->name == $role['name'] ? "selected" : "" }}
                                                                                        value="{{ $role->name }}">
                                                                                            {{ ucwords(explode($slug.'.', $role->name)[1]) }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    @endif
                                                @endcan
                                                <td class="text-center">
                                                    <x-status.user_status :status="$value['status']" />
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            <div class="px-2">
                                {{ $users->links('components.pagination') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
@endsection
