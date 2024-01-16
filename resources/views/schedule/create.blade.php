<x-app-layout>

    <script src="https://cdn.ckeditor.com/ckeditor5/37.0.1/classic/ckeditor.js"></script>

    <h1>Criar Horário</h1>
    <div>
        <form method="POST" action="{{ route('schedule.store', ['buffet'=>$buffet]) }}">
            @csrf

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Dia da semana -->
            <div>
                <label for="day_week" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Dia da semana</label>
                    <select name="day_week" id="day_week" required>
                        @foreach( App\Enums\DayWeek::array() as $key=>$day_week )
                            <option value="{{$key}}">{{$key}}</option>
                        @endforeach
                        <!-- <option value="invalid2"  disabled>Nenhum horario disponivel neste dia, tente novamente!</option> -->
                    </select>
            </div>

            <div>
                <x-input-label for="start_time" :value="__('Início da festa')" />
                <x-text-input id="start_time" class="block mt-1 w-full" type="time" name="start_time" :value="old('start_time')" required autofocus autocomplete="start_time" />
                <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
            </div>

            

            <div>
                <x-input-label for="duration" :value="__('Duração da festa')" />
                <x-text-input id="duration" class="block mt-1 w-full" type="number" name="duration" :value="old('duration')" placeholder="duração em minutos" required autofocus autocomplete="duration" />
                <x-input-error :messages="$errors->get('duration')" class="mt-2" />
            </div>

            <h2>Caso haja datas bloqueadas para esse horário coloque abaixo</h2>

            <div>
                <x-input-label for="start_block" :value="__('Data início bloqueio')" />
                <x-text-input id="start_block" class="block mt-1 w-full" type="date" name="start_block" :value="old('start_block')" autofocus autocomplete="start_block" />
                <x-input-error :messages="$errors->get('start_block')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="end_block" :value="__('Data final bloqueio')" />
                <x-text-input id="end_block" class="block mt-1 w-full" type="date" name="end_block" :value="old('end_block')" autofocus autocomplete="end_block" />
                <x-input-error :messages="$errors->get('end_block')" class="mt-2" />
            </div>


            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="ms-4">
                    Criar Horário 
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
        ClassicEditor
            .create(document.querySelector('#food_description'))
            .catch(error => {
                console.error(error);
            });
            ClassicEditor
            .create(document.querySelector('#beverages_description'))
            .catch(error => {
                console.error(error);
            });
        });
    </script>
    
</x-app-layout>