<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Perguntas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-auto">
                        <div>
                            <h1 class="inline-flex items-center border border-transparent text-lg leading-4 font-semi-bold">Listagem dos Funcionários</h1>
                            <h2><a href="{{ route('survey.create', ['buffet'=>$buffet->slug]) }}">Criar pergunta</a></h2>
                        </div>
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b-2 border-gray-200">
                                <tr>
                                    <th class="w-20 p-3 text-sm font-semibold tracking-wide text-center">ID</th>
                                    <th class="p-3 text-sm font-semibold tracking-wide text-left">Pergunta</th>
                                    <th class="p-3 text-sm font-semibold tracking-wide text-center">Respostas</th>
                                    <th class="p-3 text-sm font-semibold tracking-wide text-center">Formato</th>
                                    <th class="p-3 text-sm font-semibold tracking-wide text-center">Status</th>
                                    <th class="p-3 text-sm font-semibold tracking-wide text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @if($surveys->total() === 0)
                                <tr>
                                    <td colspan="7" class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">Nenhuma pergunta encontrada</td>
                                </tr>
                                @else   
                                    @php
                                    $limite_char = 50; // O número de caracteres que você deseja exibir
                                    $class_active = "p-1.5 text-xs font-medium uppercase tracking-wider text-green-800 bg-green-200 rounded-lg bg-opacity-50";
                                    $class_unactive = 'p-1.5 text-xs font-medium uppercase tracking-wider text-red-800 bg-red-200 rounded-lg bg-opacity-50';
                                    @endphp
                                    @foreach($surveys->items() as $value)
                                    <tr class="bg-white">
                                        <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $value['id'] }}</td>
                                        <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-left">{!! mb_strimwidth($value['question'], 0, $limite_char, " ...") !!} <a href="{{route('survey.show', ['survey'=>$value['id'], 'buffet'=>$buffet->slug])}}" class="p-1 text-xs font-medium uppercase text-green-700 bg-green-400 rounded-lg bg-opacity-50">Ver mais</a></td>
                                        <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $value['answers'] }}</td>
                                        <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ App\Enums\QuestionType::fromValue($value['question_type']) }}</td>
                                        <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center"><x-status.survey_status :status="$value['status']" /></td>
                                        <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                            <a href="{{ route('survey.show',  ['survey'=>$value['id'], 'buffet'=>$buffet->slug]) }}" title="Visualizar pergunta {{$value['id']}}">👁️</a>
                                            <a href="{{ route('survey.edit',  ['survey'=>$value['id'], 'buffet'=>$buffet->slug]) }}" title="Editar pergunta {{$value['id']}}">✏️</a>
                                            {{--<form action="{{ route('survey.change_question_status', $value['id']) }}" method="post" class="inline">
                                                @csrf
                                                @method('patch')
                                                @if($value['status'] == true)
                                                    <button type="submit" title="Deletar pergunta {{$value['id']}}">❌</button>
                                                @else
                                                    <button type="submit" title="Ativar pergunta {{$value['id']}}">✅</button>
                                                @endif
                                            </form>--}}
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        {{ $surveys->links('components.pagination') }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>