@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Pesquisa de Satisfação', 'subtitle'=>"Visualizar pesquisa de satisfação"])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Pesquisa de satisfação</h6>
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="px-4">
                            <h3>{{ $survey->main_theme }}</h3>
                            <p class="text-md mb-0"><strong>Pergunta: </strong></p>
                            <div>
                                {!! $survey->question !!}
                            </div>
                            <p class="text-lg mb-0"><strong>Status: </strong> </strong><x-status.survey_status :status="$survey->status" /></p>
                            <p class="text-lg mb-0"><strong>Formato: </strong>{{ App\Enums\QuestionType::fromValue($survey->question_type) }}</p>
                            <p class="text-lg mb-0"><strong>Respostas: </strong> {{ $survey->answers }}</p>
                                <div class="overflow-auto">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Resposta</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Reserva</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($survey->answers === 0)
                                            <tr>
                                                <td colspan="2" class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">Nenhuma Resposta encontrada</td>
                                            </tr>
                                            @else
                                                @php
                                                    $limite_char = 30; // O número de caracteres que você deseja exibir
                                                @endphp
                                                @foreach($survey['user_answers'] as $key=>$value)
                                                <tr class="bg-white">
                                                    <td class="text-center">
                                                        <div class="d-flex px-2 py-1">
                                                            <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                                <p class="mb-0 text-sm">{{ $value->answer }}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex px-2 py-1">
                                                            <div class="d-flex flex-column justify-content-center text-xxs text-center w-100">
                                                                <p class="text-sm mb-0"><a href="{{ route('booking.show', ['booking'=>$value->booking->hashed_id, 'buffet'=>$buffet->slug]) }}"  class="font-bold text-blue-500 hover:underline">{{ $value->booking['name_birthdayperson'] }}</a></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @endif
                
                                        </tbody>
                                    </table>
                                </div>

                        @can('update survey question')
                            <a href="{{ route('survey.edit', ['buffet'=>$buffet->slug, 'survey'=>$survey->hashed_id]) }}" title="Editar recomendação" class="btn btn-outline-primary btn-sm fs-6">Editar</a>
                        @endcan
                        @can('change survey question status')
                            @if($survey['status'] == true)
                                <form action="{{ route('survey.destroy', ['buffet'=>$buffet->slug, 'survey'=>$survey->hashed_id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-outline-primary btn-sm fs-6" title="Desativar recomendação">❌ Desativar pergunta</button>                                        
                                </form>
                            @else
                                <form action="{{ route('survey.change_status', ['survey'=>$survey['hashed_id'], 'buffet'=>$buffet->slug]) }}" method="post" class="d-inline">
                                    @csrf
                                    @method('patch')
                                    <input type="hidden" name="status" survey="true">
                                    <button type="submit" title="Ativar recomendação" class="btn btn-outline-primary btn-sm fs-6">✅ Ativar pergunta</button>
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
