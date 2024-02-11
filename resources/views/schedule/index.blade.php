<x-app-layout>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    @endif
                    <div class="overflow-auto">
                        <div>
                            <h1 class="inline-flex items-center border border-transparent text-lg leading-4 font-semi-bold">Listagem dos horários de festas</h1>
                            <h2><a href="{{ route('schedule.create', ['buffet'=> $buffet]) }}">Criar horário</a></h2>
                        </div>
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <!-- w-24 p-3 text-sm font-semibold tracking-wide text-left -->
                                
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
                                @endphp
                                @foreach($schedules as $value)
                                <tr class="bg-white">
                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                    <a href="{{ route('schedule.show', ['schedule'=>$value['hashed_id'], 'buffet'=>$buffet]) }}" class="font-bold text-blue-500 hover:underline">{{ App\Enums\DayWeek::getEnumByName($value['day_week']) }}</a>
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
                                        <a href="{{ route('schedule.show', ['schedule'=>$value['hashed_id'], 'buffet'=>$buffet]) }}" title="Visualizar '{{ App\Enums\DayWeek::getEnumByName($value['day_week']) }}'">👁️</a>
                                        @if($value['status'] === App\Enums\ScheduleStatus::ACTIVE->name)
                                            <a href="{{ route('schedule.edit', ['schedule'=>$value['hashed_id'], 'buffet'=>$buffet]) }}" title="Editar '{{ App\Enums\DayWeek::getEnumByName($value['day_week']) }}'">✏️</a>
                                        @endif
                                        @if($value['status'] !== App\Enums\ScheduleStatus::UNACTIVE->name)
                                            <form action="{{ route('schedule.destroy', ['schedule'=>$value['hashed_id'], 'buffet'=>$buffet]) }}" method="post" class="inline">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" title="Deletar '{{ $value['start_time'] }}'">❌</button>
                                            </form>
                                            @else
                                            <form action="{{ route('schedule.change_status', ['schedule'=>$value['hashed_id'], 'buffet'=>$buffet]) }}" method="post" class="inline">
                                                @csrf
                                                @method('patch')
                                                <input type="hidden" name="status" value="{{App\Enums\ScheduleStatus::ACTIVE->name }}">
                                                <button type="submit" title="Ativar '{{ $value['start_time'] }}'">✅</button>
                                            </form>
                                        @endif                                        
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