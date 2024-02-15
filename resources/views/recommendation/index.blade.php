@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Recomendações'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Recomendações de festas</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Conteúdo</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Status</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($recommendations) === 0)
                                    <tr>
                                        <td colspan="3" class="p-3 text-sm text-center">Nenhuma recomendação encontrada</td>
                                    </tr>
                                    @else
                                        @php
                                            $limite_char = 90; // O número de caracteres que você deseja exibir
                                        @endphp
                                        @foreach($recommendations as $value)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center text-xxs">
                                                        {!! mb_strimwidth($value['content'], 0, $limite_char, " ...") !!}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <x-status.recommendation_status :status="$value['status']" />
                                            </td>
                                            <td class="align-middle">
                                                @can('view recommendation')
                                                    <a href="{{ route('recommendation.show', ['buffet'=>$buffet->slug,'recommendation'=>$value->hashed_id]) }}" title="Visualizar recomendação" class="btn btn-outline-primary btn-sm fs-6">👁️</a>
                                                @endcan
                                                @can('update recommendation')
                                                    <a href="{{ route('recommendation.edit', ['buffet'=>$buffet->slug, 'recommendation'=>$value->hashed_id]) }}" title="Editar recomendação" class="btn btn-outline-primary btn-sm fs-6">✏️</a>
                                                @endcan
                                                @can('change recommendation status')
                                                    @if($value['status'] !== App\Enums\RecommendationStatus::UNACTIVE->name)
                                                        <form action="{{ route('recommendation.destroy', ['buffet'=>$buffet->slug, 'recommendation'=>$value->hashed_id]) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit" class="btn btn-outline-primary btn-sm fs-6" title="Desativar recomendação" >❌</button>                                        
                                                        </form>
                                                    @else
                                                        <form action="{{ route('recommendation.change_status', ['recommendation'=>$value['hashed_id'], 'buffet'=>$buffet->slug]) }}" method="post" class="d-inline">
                                                            @csrf
                                                            @method('patch')
                                                            <input type="hidden" name="status" value="{{App\Enums\RecommendationStatus::ACTIVE->name }}">
                                                            <button type="submit" title="Ativar recomendação" class="btn btn-outline-primary btn-sm fs-6">✅</button>
                                                        </form>
                                                    @endif    
                                                @endcan
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            {{ $recommendations->links('components.pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
@endsection
