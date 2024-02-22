@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'decoraçãos', 'subtitle'=>"Visualizar decoração"])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Pacotes de decoração</h6>
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="px-4">
                            <h3>{{ $decoration->main_theme }}</h3>
                            <p class="text-md mb-0"><strong>Slug:</strong> {{ $decoration->slug }}</p>
                            <p class="text-lg mb-0"><strong>Status:</strong> <x-status.decoration_status :status="$decoration['status']" /></p>
                            <p class="text-lg mb-0"><span class="badge bg-gradient-primary"><strong>Preço:</strong> R$ {{ $decoration->price }}</span></p>

                            <div class="accordion-1">
                                <div class="row">
                                    <div class="accordion" id="accordionRental">
                                        <div class="accordion-item mb-2">
                                            <h5 class="accordion-header" id="headingOne">
                                                <button class="accordion-button border-bottom font-weight-bold collapsed ps-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                                    Descrição das decorações
                                                <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                                <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                                                </button>
                                            </h5>
                                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionRental" style="">
                                                <div class="accordion-body text-sm opacity-8">
                                                    {!! $decoration->description !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        @foreach($decoration_photos as $key=>$photo)
                            <img {{ $key == 0 ? 'active' : ''}}" src="{{ asset('storage/decorations'. $photo->file_path) }}" alt="{{ $photo->file_name }}"> 
                        @endforeach
                        <br>
                        <br>
                        @can('update decoration')
                            <a href="{{ route('decoration.edit', ['buffet'=>$buffet->slug, 'decoration'=>$decoration->slug]) }}" title="Editar recomendação" class="btn btn-outline-primary btn-sm fs-6">Editar</a>
                        @endcan
                        @can('change decoration status')
                            @if($decoration['status'] !== App\Enums\DecorationStatus::UNACTIVE->name)
                                <form action="{{ route('decoration.destroy', ['buffet'=>$buffet->slug, 'decoration'=>$decoration->slug]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-outline-primary btn-sm fs-6" title="Desativar recomendação">❌ Desativar pacote de decoração</button>                                        
                                </form>
                            @else
                                <form action="{{ route('decoration.change_status', ['decoration'=>$decoration['slug'], 'buffet'=>$buffet->slug]) }}" method="post" class="d-inline">
                                    @csrf
                                    @method('patch')
                                    <input type="hidden" name="status" value="{{App\Enums\DecorationStatus::ACTIVE->name }}">
                                    <button type="submit" title="Ativar recomendação" class="btn btn-outline-primary btn-sm fs-6">✅ Ativar pacote de decoração</button>
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
