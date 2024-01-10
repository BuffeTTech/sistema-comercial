<x-app-layout>

    <div class="py-12">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900">
                    <div>
                        <p><strong><a href="{{route('decoration.create',['buffet'=>$buffet])}}">Criar nova Decoração</a></strong></p>
                    </div>
                    <div class="overflow-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <!-- w-24 p-3 text-sm font-semibold tracking-wide text-left -->
                                
                                <th class="w-20 p-3 text-sm font-semibold tracking-wide text-center">ID</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-left">Nome da decoração</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Descrição</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Slug</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Preço</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Status</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Ações</th>

                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if(count($decorations) === 0)
                            <tr>
                                <td colspan="8" class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">Nenhum pacote encontrado</td>
                            </tr>
                            @else
                                @php
                                    $limite_char = 30; // O número de caracteres que você deseja exibir
                                    $class_active = "p-1.5 text-xs font-medium uppercase tracking-wider text-green-800 bg-green-200 rounded-lg bg-opacity-50";
                                    $class_unactive = 'p-1.5 text-xs font-medium uppercase tracking-wider text-red-800 bg-red-200 rounded-lg bg-opacity-50';
                                @endphp
                                @foreach($decorations as $value)
                                <tr class="bg-white">
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $value['id'] }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                    <a href="{{ route('decoration.show', ['buffet'=>$buffet,'decoration'=>$value->slug]) }}" class="font-bold text-blue-500 hover:underline">{{ $value['main_theme'] }}</a>
                                    </td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap">{!! mb_strimwidth($value['description'], 0, $limite_char, " ...") !!}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $value['slug'] }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">R$ {{ (float)$value['price'] }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center"><x-status.decoration_status :status="$value['status']" /></td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                        <a href="{{ route('decoration.show', ['buffet'=>$buffet,'decoration'=>$value->slug]) }}" title="Visualizar '{{$value['main_theme']}}'">👁️</a>
                                        <a href="{{ route('decoration.edit', ['buffet'=>$buffet, 'decoration'=>$value->slug]) }}" title="Editar '{{$value['main_theme']}}'">✏️</a>
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