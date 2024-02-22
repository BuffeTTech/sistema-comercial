@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Recomendações', 'subtitle'=>"Visualizar Recomendação"])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Recomendações de festas</h6>
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="px-4">
                            <h5>Conteudo</h5>
                            {!! $recommendation->content !!}
                            @can('update recommendation')
                                <a href="{{ route('recommendation.edit', ['buffet'=>$buffet->slug, 'recommendation'=>$recommendation->hashed_id]) }}" title="Editar recomendação" class="btn btn-outline-primary btn-sm fs-6">Editar</a>
                            @endcan
                            @can('change recommendation status')
                                @if($recommendation['status'] !== App\Enums\RecommendationStatus::UNACTIVE->name)
                                    <form action="{{ route('recommendation.destroy', ['buffet'=>$buffet->slug, 'recommendation'=>$recommendation->hashed_id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-outline-primary btn-sm fs-6" title="Desativar recomendação">❌ Desativar Recomendação</button>                                        
                                    </form>
                                @else
                                    <form action="{{ route('recommendation.change_status', ['recommendation'=>$recommendation['hashed_id'], 'buffet'=>$buffet->slug]) }}" method="post" class="d-inline">
                                        @csrf
                                        @method('patch')
                                        <input type="hidden" name="status" recommendation="{{App\Enums\RecommendationStatus::ACTIVE->name }}">
                                        <button type="submit" title="Ativar recomendação" class="btn btn-outline-primary btn-sm fs-6">✅ Ativar Recomendação</button>
                                    </form>
                                @endif  
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
@endsection
