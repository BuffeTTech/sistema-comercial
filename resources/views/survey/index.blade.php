@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Pesquisa de satisfação'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between">
                        <h6>Perguntas</h6>
                        @if($total < $configurations['max_survey_questions'])
                            <a href="{{ route('survey.create', ['buffet'=>$buffet->slug]) }}" class="btn btn-outline-primary btn-sm fs-6 btn-tooltip" title="Criar recomendação">Criar Pesquisa</a>                                        
                        @endif
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
                                            Pergunta</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                            Respostas</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                            Formato</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Status</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($surveys) === 0)
                                    <tr>
                                        <td colspan="3" class="p-3 text-sm text-center">Nenhuma pergunta encontrada</td>
                                    </tr>
                                    @else
                                        @php
                                            $limite_char = 70; // O número de caracteres que você deseja exibir
                                        @endphp
                                        @foreach($surveys as $value)
                                        <tr>
                                            <td class="text-center">
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center text-xxs">
                                                        <h6 class="mb-0 text-sm">{!! mb_strimwidth($value['question'], 0, $limite_char, " ...") !!}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center"> 
                                                <div>
                                                    <div class="d-flex flex-column justify-content-center text-xxs w-100">
                                                        <p class="text-sm mb-0">{{ $value['answers'] }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div>
                                                    <div class="d-flex flex-column justify-content-center text-xxs w-100">
                                                        <p class="text-sm mb-0">{{ App\Enums\QuestionType::fromValue($value['question_type']) }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <x-status.survey_status :status="$value['status']" />
                                            </td>
                                            <td class="align-middle">
                                                @can('view survey')
                                                    <a href="{{ route('survey.show', ['buffet'=>$buffet->slug,'survey'=>$value->hashed_id]) }}" title="Visualizar pergunta" class="btn btn-outline-primary btn-sm fs-6">👁️</a>
                                                @endcan
                                                @can('update survey')
                                                    <a href="{{ route('survey.edit', ['buffet'=>$buffet->slug, 'survey'=>$value->hashed_id]) }}" title="Editar pergunta" class="btn btn-outline-primary btn-sm fs-6">✏️</a>
                                                @endcan
                                                @can('change survey status')
                                                    @if($value['status'] == true)
                                                        <form action="{{ route('survey.destroy', ['buffet'=>$buffet->slug, 'survey'=>$value->hashed_id]) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit" class="btn btn-outline-primary btn-sm fs-6" title="Desativar pergunta" >❌</button>                                        
                                                        </form>
                                                    @else
                                                        <form action="{{ route('survey.change_status', ['survey'=>$value['hashed_id'], 'buffet'=>$buffet->slug]) }}" method="post" class="d-inline">
                                                            @csrf
                                                            @method('patch')
                                                            <input type="hidden" name="status" value="true">
                                                            <button type="submit" title="Ativar pergunta" class="btn btn-outline-primary btn-sm fs-6">✅</button>
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
                                {{ $surveys->links('components.pagination') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
@endsection