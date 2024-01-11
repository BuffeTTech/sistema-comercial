<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-auto">
                        <div>
                            <h1 class="inline-flex items-center border border-transparent text-lg leading-4 font-semi-bold">Listagem dos horários de festas</h1>
                            <h2><a href="{{ route('schedule.create', ['buffet'=> $buffet]) }}">Criar horário</a></h2>
                        </div>
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <!-- w-24 p-3 text-sm font-semibold tracking-wide text-left -->
                                
                                <th class="w-20 p-3 text-sm font-semibold tracking-wide text-center">ID</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-left">Dia da semana</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Hora de início</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Duração</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Início de bloqueio</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Final de bloqueio</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Status</th>
                                <th class="p-3 text-sm font-semibold tracking-wide text-center">Ações</th>

                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if(count($schedules) === 0)
                            <tr>
                                <td colspan="8" class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">Nenhum horário encontrado</td>
                            </tr>
                            @else
                                @php
                                    $limite_char = 30; // O número de caracteres que você deseja exibir
                                    $class_active = "p-1.5 text-xs font-medium uppercase tracking-wider text-green-800 bg-green-200 rounded-lg bg-opacity-50";
                                    $class_unactive = 'p-1.5 text-xs font-medium uppercase tracking-wider text-red-800 bg-red-200 rounded-lg bg-opacity-50';
                                @endphp
                                @foreach($schedules as $value)
                                <tr class="bg-white">
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $value['id'] }}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                    <a href="{{ route('schedule.show', ['schedule'=>$value['id'], 'buffet'=>$buffet]) }}" class="font-bold text-blue-500 hover:underline">{{ $value['day_week'] }}</a>
                                    </td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap">{{$value['start_time']}}</td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{$value['duration']}} minutos</td>
                                    @if($value['start_block']=== null)
                                        <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">Não existe início do bloqueio</td>
                                    @else
                                        <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{$value['start_block'] }}</td>
                                    @endif
                                    @if($value['start_block']=== null)
                                        <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">Não existe final do bloqueio</td>
                                    @else
                                        <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{$value['end_block'] }}</td>
                                    @endif
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center"><x-status.schedule_status :status="$value['status']" /></td>
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                        <a href="{{ route('schedule.show', ['schedule'=>$value['id'], 'buffet'=>$buffet]) }}" title="Visualizar '{{$value['day_week']}}'">👁️</a>
                                        <a href="{{ route('schedule.edit', ['schedule'=>$value['id'], 'buffet'=>$buffet]) }}" title="Editar '{{$value['day_week']}}'">✏️</a>
                                        <a href="{{ route('schedule.destroy', ['schedule'=>$value['id'], 'buffet'=>$buffet]) }}" title="Deletar '{{$value['day_week']}}'">❌</a>
                                    </td>
                                </tr>
                                @endforeach
                            @endif

                        </tbody>
                    </table>
                    {{ $schedules->links('components.pagination') }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>