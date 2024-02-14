<x-app-layout>

    <div class="py-12">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900">
                    <div>
                        @if(count($recommendations) < $configurations['max_recommendations'])
                            <p><strong><a href="{{ route('recommendation.create', ['buffet'=>$buffet->slug]) }}">Criar recomendação</a></strong></p>
                        @endif
                    </div>
                    <div class="overflow-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <!-- w-24 p-3 text-sm font-semibold tracking-wide text-left -->
                                
                                <th class="p-3 text-sm font-semibold tracking-wide text-left">Conteúdo da decoração</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-left">Status</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Ações</th>

                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if(count($recommendations) === 0)
                            <tr>
                                <td colspan="8" class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">Nenhuma recomendação encontrada</td>
                            </tr>
                            @else
                                @php
                                    $limite_char = 30; // O número de caracteres que você deseja exibir
                                    $class_active = "p-1.5 text-xs font-medium uppercase tracking-wider text-green-800 bg-green-200 rounded-lg bg-opacity-50";
                                    $class_unactive = 'p-1.5 text-xs font-medium uppercase tracking-wider text-red-800 bg-red-200 rounded-lg bg-opacity-50';
                                @endphp
                                @foreach($recommendations as $value)
                                <tr class="bg-white">
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                        <a href="{{ route('recommendation.show', ['buffet'=>$buffet->slug,'recommendation'=>$value->hashed_id]) }}" class="font-bold text-blue-500 hover:underline">{{ $value['content'] }}</a>
                                    </td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center"><x-status.recommendation_status :status="$value['status']" /></td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                        <a href="{{ route('recommendation.show', ['buffet'=>$buffet->slug,'recommendation'=>$value->hashed_id]) }}" title="Visualizar recomendação">👁️</a>
                                        <a href="{{ route('recommendation.edit', ['buffet'=>$buffet->slug, 'recommendation'=>$value->hashed_id]) }}" title="Editar recomendação">✏️</a>
                                        @if($value['status'] !== App\Enums\RecommendationStatus::UNACTIVE->name)
                                            <form action="{{ route('recommendation.destroy', ['buffet'=>$buffet->slug, 'recommendation'=>$value->hashed_id]) }}" method="POST" class="inline">
                                                @csrf
                                                @method('delete')
                                                <button type="submit">❌</button>                                        
                                            </form>
                                        @else
                                            <form action="{{ route('recommendation.change_status', ['recommendation'=>$value['hashed_id'], 'buffet'=>$buffet->slug]) }}" method="post" class="inline">
                                                @csrf
                                                @method('patch')
                                                <input type="hidden" name="status" value="{{App\Enums\RecommendationStatus::ACTIVE->name }}">
                                                <button type="submit" title="Ativar '{{ $value['start_time'] }}'">✅</button>
                                            </form>
                                        @endif    

                                        <!-- Se a pessoa está vendo esta página, ela por padrão ja é ADM ou comercial, logo nao preciso validar aqui! -->

                                    </td>
                                </tr>
                                @endforeach
                            @endif

                        </tbody>
                    </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>