@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Comidas'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between">
                        <h6>Comidas de festa</h6>
                        <a href="{{ route('food.create', ['buffet'=>$buffet->slug]) }}" class="btn btn-outline-primary btn-sm fs-6 btn-tooltip" title="Criar Comida">Criar Comida</a>                                        
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
                                            Nome do pacote</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Descrição das comidas</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Descriçao das bebidas</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Preço do pacote</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Slug</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Status</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($foods) === 0)
                                    <tr>
                                        <td colspan="3" class="p-3 text-sm text-center">Nenhuma comida encontrada</td>
                                    </tr>
                                    @else
                                        @php
                                            $limite_char = 30; // O número de caracteres que você deseja exibir
                                        @endphp
                                        @foreach($foods as $value)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center text-xxs w-100">
                                                        <h6 class="mb-0 text-sm">{{ $value['name_food'] }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="d-flex flex-column justify-content-center text-xxs w-100">
                                                        <p class="text-sm mb-0">{{ mb_strimwidth($value['food_description'], 0, $limite_char, " ...") }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="d-flex flex-column justify-content-center text-xxs w-100">
                                                        <p class="text-sm mb-0">{{ mb_strimwidth($value['beverages_description'], 0, $limite_char, " ...") }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="d-flex flex-column justify-content-center text-xxs w-100">
                                                        <p class="text-sm mb-0">{{ (float)$value['price'] }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="d-flex flex-column justify-content-center text-xxs w-100">
                                                        <p class="text-sm mb-0">{{ $value['slug'] }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <x-status.food_status :status="$value['status']" />
                                            </td>
                                            <td class="align-middle">
                                                @can('view food')
                                                    <a href="{{ route('food.show', ['buffet'=>$buffet->slug,'food'=>$value->slug]) }}" title="Visualizar recomendação" class="btn btn-outline-primary btn-sm fs-6">👁️</a>
                                                @endcan
                                                @can('update food')
                                                    <a href="{{ route('food.edit', ['buffet'=>$buffet->slug, 'food'=>$value->slug]) }}" title="Editar recomendação" class="btn btn-outline-primary btn-sm fs-6">✏️</a>
                                                @endcan
                                                @can('change food status')
                                                    @if($value['status'] !== App\Enums\FoodStatus::UNACTIVE->name)
                                                        <form action="{{ route('food.destroy', ['buffet'=>$buffet->slug, 'food'=>$value->slug]) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit" class="btn btn-outline-primary btn-sm fs-6" title="Desativar recomendação" >❌</button>                                        
                                                        </form>
                                                    @else
                                                        <form action="{{ route('food.change_status', ['food'=>$value['hashed_id'], 'buffet'=>$buffet->slug]) }}" method="post" class="d-inline">
                                                            @csrf
                                                            @method('patch')
                                                            <input type="hidden" name="status" value="{{App\Enums\FoodStatus::ACTIVE->name }}">
                                                            <button type="submit" title="Ativar comida" class="btn btn-outline-primary btn-sm fs-6">✅</button>
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
                                {{ $foods->links('components.pagination') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
@endsection