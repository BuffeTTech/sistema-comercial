@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Comidas', 'subtitle'=>"Visualizar comida"])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Pacotes de comida</h6>
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="px-4">
                            <h3>{{ $food->name_food }}</h3>
                            <p class="text-md mb-0"><strong>Slug:</strong> {{ $food->slug }}</p>
                            <p class="text-lg mb-0"><strong>Status:</strong> <x-status.food_status :status="$food['status']" /></p>
                            <p class="text-lg mb-0"><span class="badge bg-gradient-primary"><strong>Preço:</strong> R$ {{ $food->price }}</span></p>

                            <div class="accordion-1">
                                <div class="row">
                                    <div class="accordion" id="accordionRental">
                                        <div class="accordion-item mb-2">
                                            <h5 class="accordion-header" id="headingOne">
                                                <button class="accordion-button border-bottom font-weight-bold collapsed ps-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                                    Descrição das comidas
                                                <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                                <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                                </button>
                                            </h5>
                                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionRental" style="">
                                                <div class="accordion-body text-sm opacity-8">
                                                    {!! $food->food_description !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item mb-2">
                                            <h5 class="accordion-header" id="headingTwo">
                                                <button class="accordion-button border-bottom font-weight-bold collapsed ps-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                    Descrição das bebidas
                                                <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                                <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                                </button>
                                            </h5>
                                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionRental" style="">
                                                <div class="accordion-body text-sm opacity-8">
                                                    {!! $food->beverages_description !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        @foreach($foods_photo as $key=>$photo)
                            <img {{ $key == 0 ? 'active' : ''}}" src="{{ asset('storage/foods'. $photo->file_path) }}" alt="{{ $photo->file_name }}"> 
                        @endforeach
                        <br>
                        <br>
                        @can('update food')
                            <a href="{{ route('food.edit', ['buffet'=>$buffet->slug, 'food'=>$food->slug]) }}" title="Editar recomendação" class="btn btn-outline-primary btn-sm fs-6">Editar</a>
                        @endcan
                        @can('change food status')
                            @if($food['status'] !== App\Enums\FoodStatus::UNACTIVE->name)
                                <form action="{{ route('food.destroy', ['buffet'=>$buffet->slug, 'food'=>$food->slug]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-outline-primary btn-sm fs-6" title="Desativar recomendação">❌ Desativar pacote de comida</button>                                        
                                </form>
                            @else
                                <form action="{{ route('food.change_status', ['food'=>$food['hashed_id'], 'buffet'=>$buffet->slug]) }}" method="post" class="d-inline">
                                    @csrf
                                    @method('patch')
                                    <input type="hidden" name="status" food="{{App\Enums\FoodStatus::ACTIVE->name }}">
                                    <button type="submit" title="Ativar recomendação" class="btn btn-outline-primary btn-sm fs-6">✅ Ativar pacote de comida</button>
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
