<x-app-layout>
    
    <script src="https://cdn.ckeditor.com/ckeditor5/37.0.1/classic/ckeditor.js"></script>

    <h1>Editar Pacote</h1>

    <div>
        <form method="POST" action="{{ route('schedule.update', ['buffet'=>$buffet, 'schedule'=>$schedule->hashed_id]) }}" >
            @method('put')
            @csrf

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div>
                <label for="day_week" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Dia da semana</label>
                    <select name="day_week" id="day_week" required>
                        @foreach( App\Enums\DayWeek::array() as $key=>$day_week )
                            <option value="{{$day_week}}" {{ $day_week == $schedule->day_week ? 'selected' : ''}}>{{$key}}</option>
                        @endforeach
                        <!-- <option value="invalid2"  disabled>Nenhum horario disponivel neste dia, tente novamente!</option> -->
                    </select>
            </div>

            @php
                $date = DateTime::createFromFormat('H:i:s', $schedule->start_time);
                $date = $date->format('H:i');
            @endphp

            <div>
                <x-input-label for="start_time" :value="__('Início da festa')" />
                <x-text-input id="start_time" class="block mt-1 w-full" type="time" name="start_time" :value="$date" required autofocus autocomplete="start_time" />
                <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="duration" :value="__('Duração da festa')" />
                <x-text-input id="duration" class="block mt-1 w-full" type="number" name="duration" :value="old('duration') ?? $schedule->duration" placeholder="duração em minutos" required autofocus autocomplete="duration" />
                <x-input-error :messages="$errors->get('duration')" class="mt-2" />
            </div>

            <h2>Caso haja datas bloqueadas para esse horário coloque abaixo</h2>

            <div>
                <x-input-label for="start_block" :value="__('Data início bloqueio')" />
                <x-text-input id="start_block" class="block mt-1 w-full" type="date" name="start_block" :value="old('start_block') ?? $schedule->start_block" autofocus autocomplete="start_block" />
                <x-input-error :messages="$errors->get('start_block')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="end_block" :value="__('Data final bloqueio')" />
                <x-text-input id="end_block" class="block mt-1 w-full" type="date" name="end_block" :value="old('end_block') ?? $schedule->end_block" autofocus autocomplete="end_block" />
                <x-input-error :messages="$errors->get('end_block')" class="mt-2" />
            </div>
           


            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="ms-4">
                    {{ __('Update') }}
                </x-primary-button>
            </div>
        </form>

</x-app-layout>