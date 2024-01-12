<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 float-left" style="width: 50%; border-right: 3px solid #000000;">
                    <div class="bg-gray-50 border-b-2 border-gray-200">
                        <p><strong>Dia da semana</strong> {{ $schedule->day_week }}</p><br>
                        <p><strong>Horário de início</strong> {{ $schedule->start_time}}</p><br>
                        <p><strong>Duração</strong> {{ $schedule->duration }}</p><br>
                        @if($schedule['start_block']!== null)
                            <p><strong>Início do bloqueio</strong> {{ $schedule->start_block }}</p><br>
                        
                        @endif 
                        @if ($schedule['end_block']!== null)
                            <p><strong>Final do bloqueio</strong> {{ $schedule->end_block }}</p><br>
                        @endif
                        @php
                        $class_active = "p-1.5 text-xs font-medium uppercase tracking-wider text-green-800 bg-green-200 rounded-lg bg-opacity-50";
                        $class_unactive = 'p-1.5 text-xs font-medium uppercase tracking-wider text-red-800 bg-red-200 rounded-lg bg-opacity-50';
                        @endphp
                        <p><strong>Status:</strong>
                            <form action="{{ route('schedule.change_status', ['buffet' => $buffet, 'schedule' => $schedule['id']]) }}" method="post" class="inline">
                                @csrf
                                @method('patch')

                                <label for="status" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"></label>
                                <select name="status" id="status" required onchange="this.form.submit()">
                                    @foreach( App\Enums\ScheduleStatus::array() as $key=>$status )
                                        <option value="{{$status}}" {{ $schedule['status'] == $status ? 'selected' : ""}}>{{$key}}</option>
                                    @endforeach
                                    <!-- <option value="invalid2"  disabled>Nenhum horario disponivel neste dia, tente novamente!</option> -->
                                </select>
                            </form>
                        </p><br>
                    </div>
                    <br><br>

                    <div class="flex items-center ml-auto float-down">
                        <a href="{{ route('schedule.edit', ['schedule'=>$schedule->id, 'buffet'=>$buffet]) }}" class="bg-amber-300 hover:bg-amber-500 text-black font-bold py-2 px-4 rounded">
                            <div class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4">
                                Editar
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
