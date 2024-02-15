@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Decorações'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between">
                        <h6>Decorações das festas</h6>
                        <a href="{{ route('decoration.create', ['buffet'=>$buffet->slug]) }}" class="btn btn-outline-primary btn-sm fs-6 btn-tooltip" title="Criar decoração">Criar Decoração</a>                                        
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Nome da Decoração</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Descrição</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Slug</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Preço</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Status</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($decorations) === 0)
                                    <tr>
                                        <td colspan="3" class="p-3 text-sm text-center">Nenhuma decoração encontrada</td>
                                    </tr>
                                    @else
                                        @php
                                            $limite_char = 90; // O número de caracteres que você deseja exibir
                                        @endphp
                                        @foreach($decorations as $value)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                        <h6 class="text-sm mb-0">{{$value['main_theme']}}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center text-xxs">
                                                        <p class="text-sm mb-0">{{ mb_strimwidth($value['description'], 0, $limite_char, " ...") }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                        <p class="text-sm mb-0">{{$value['slug']}}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                        <p class="text-sm mb-0">{{(float)$value['price']}}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <x-status.decoration_status :status="$value['status']" />
                                            </td>
                                            <td class="align-middle">
                                                @can('view decoration')
                                                    <a href="{{ route('decoration.show', ['buffet'=>$buffet->slug,'decoration'=>$value->slug]) }}" title="Visualizar decoração" class="btn btn-outline-primary btn-sm fs-6">👁️</a>
                                                @endcan
                                                @can('update decoration')
                                                    <a href="{{ route('decoration.edit', ['buffet'=>$buffet->slug, 'decoration'=>$value->slug]) }}" title="Editar decoração" class="btn btn-outline-primary btn-sm fs-6">✏️</a>
                                                @endcan
                                                @can('change decoration status')
                                                    @if($value['status'] !== App\Enums\Decorationstatus::UNACTIVE->name)
                                                        <form action="{{ route('decoration.destroy', ['buffet'=>$buffet->slug, 'decoration'=>$value->slug]) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit" class="btn btn-outline-primary btn-sm fs-6" title="Desativar decoração" >❌</button>                                        
                                                        </form>
                                                    @else
                                                        <form action="{{ route('decoration.change_status', ['decoration'=>$value['slug'], 'buffet'=>$buffet->slug]) }}" method="post" class="d-inline">
                                                            @csrf
                                                            @method('patch')
                                                            <input type="hidden" name="status" value="{{App\Enums\Decorationstatus::ACTIVE->name }}">
                                                            <button type="submit" title="Ativar Decoração" class="btn btn-outline-primary btn-sm fs-6">✅</button>
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
                                {{ $decorations->links('components.pagination') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
@endsection
