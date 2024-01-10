<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-auto">
                        <div>
                            <h1 class="inline-flex items-center border border-transparent text-lg leading-4 font-semi-bold">Listagem dos pacotes de comidas e bebidas</h1>
                            <h2><a href="{{ route('food.create', ['buffet'=> $buffet]) }}">Criar pacote de Comidas e Bebidas</a></h2>
                        </div>
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <!-- w-24 p-3 text-sm font-semibold tracking-wide text-left -->
                                
                                <th class="w-20 p-3 text-sm font-semibold tracking-wide text-center">ID</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-left">Nome do Pacote</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Descrição das comidas</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Descrição das bebidas</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Preço do pacote</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Slug</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Status</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Ações</th>

                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if(count($foods) === 0)
                            <tr>
                                <td colspan="8" class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">Nenhum pacote encontrado</td>
                            </tr>
                            @else
                                @php
                                    $limite_char = 30; // O número de caracteres que você deseja exibir
                                    $class_active = "p-1.5 text-xs font-medium uppercase tracking-wider text-green-800 bg-green-200 rounded-lg bg-opacity-50";
                                    $class_unactive = 'p-1.5 text-xs font-medium uppercase tracking-wider text-red-800 bg-red-200 rounded-lg bg-opacity-50';
                                @endphp
                                @foreach($foods as $value)
                                <tr class="bg-white">
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $value['id'] }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                    <a href="{{ route('food.show', ['food'=>$value['slug'], 'buffet'=>$buffet]) }}" class="font-bold text-blue-500 hover:underline">{{ $value['name_food'] }}</a>
                                    </td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap">{!! mb_strimwidth($value['food_description'], 0, $limite_char, " ...") !!}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{!! mb_strimwidth($value['beverages_description'], 0, $limite_char, " ...") !!}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">R$ {{ (float)$value['price'] }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $value['slug'] }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center"><x-status.food_status :status="$value['status']" /></td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                        <a href="{{ route('food.show', ['food'=>$value['slug'], 'buffet'=>$buffet]) }}" title="Visualizar '{{$value['name_food']}}'">👁️</a>
                                        <a href="{{ route('food.edit', ['food'=>$value['slug'], 'buffet'=>$buffet]) }}" title="Editar '{{$value['name_food']}}'">✏️</a>
                                        
                                        <!-- Se a pessoa está vendo esta página, ela por padrão ja é ADM ou comercial, logo nao preciso validar aqui! -->

                                    </td>
                                </tr>
                                @endforeach
                            @endif

                        </tbody>
                    </table>
                    {{ $foods->links('components.pagination') }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>