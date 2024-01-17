<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold mb-4">Agendar reserva</h1>
                    <form method="POST" action="{{ route('booking.store', ['buffet'=>$buffet->slug]) }}">
                        @csrf

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div>
                            <x-input-label for="name_birthdayperson" :value="__('Nome do aniversariante')" class="dark:text-slate-800"/>
                            <x-text-input id="name_birthdayperson" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="text" name="name_birthdayperson" :value="old('name_birthdayperson')" required autofocus placeholder="Nome do aniversariante" />
                            <x-input-error :messages="$errors->get('name_birthdayperson')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="years_birthdayperson" :value="__('Idade do aniversariante')" class="dark:text-slate-800"/>
                            <x-text-input id="years_birthdayperson" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="number" min="0" step="1" name="years_birthdayperson" :value="old('years_birthdayperson')" required autofocus placeholder="Idade do aniversariante" />
                            <x-input-error :messages="$errors->get('years_birthdayperson')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="num_guests" :value="__('Quantidade de convidados')" class="dark:text-slate-800"/>
                            <x-text-input id="num_guests" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="number" min="0" step="1" name="num_guests" :value="old('num_guests')" required autofocus placeholder="Quantidade de convidados" />
                            <x-input-error :messages="$errors->get('num_guests')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="party_day" :value="__('Dia da festa')" class="dark:text-slate-800"/>
                            <x-text-input id="party_day" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="date" name="party_day" :value="old('party_day')" required autofocus placeholder="Dia da festa" />
                            <x-input-error :messages="$errors->get('party_day')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="schedule_id" :value="__('Horário da festa')" class="dark:text-slate-800"/>
                            <select name="schedule_id" id="schedule_id" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" required autofocus placeholder="Horário da festa">
                                <option value="invalid" selected disabled>Selecione um horario disponível</option>
                            </select>
                            <x-input-error :messages="$errors->get('schedule_id')" class="mt-2" />
                            <span class="text-sm text-red-600 dark:text-red-400 space-y-1" id="schedule-error"></span>
                        </div>

                        {{-- 
                        buffet_id
                        food_id
                        price_food
                        decoration_id
                        price_decoration
                        price_schedule
                        discount 
                        --}}

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Adcionar Decoração') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const SITEURL = "{{ url('/') }}";
        const csrf = document.querySelector('meta[name="csrf-token"]').content
        
        const party_day = document.querySelector("#party_day")
        const party_time = document.querySelector("#schedule_id")
        const schedule = document.querySelector("#schedule_id")
        
        party_day.addEventListener('change', async function() {
            const agora = new Date();
            const escolhida = new Date(this.value + 'T00:00:00');
            while (party_time.options.length > 1) {
                party_time.remove(1); // Remova a segunda opção em diante (índice 1)
            }
            agora.setDate(agora.getDate() + 5);
            if (escolhida < agora) {
                const data = agora.toISOString().split('T')[0]
                this.value = data;
                error(`Você só pode marcar festas após 5 dias contados a partir da data de hoje (${data}).`)
                return;
            }

            const dates = await getDates(this.value)

            printDates(dates)

        });

        async function getDates(day) {
            const csrf = document.querySelector('meta[name="csrf-token"]').content
            const data = await axios.get(SITEURL + '/api/{{$buffet->slug}}/booking/schedule/' + day, {
                headers: {
                    'X-CSRF-TOKEN': csrf
                }
            })

            return data.data;
        }

        function printDates(dates) {
            const schedules = dates.schedules
            const options = schedules.map((date) => {
                const party_date = new Date("1970-01-01T" + date.start_time + "Z");
                party_date.setMinutes(party_date.getMinutes() + date.duration);
                var horaFinal = party_date.toISOString().substr(11, 8);
                return {
                    msg: `${date.start_time} - ${horaFinal}`,
                    value: date.id
                }
            })

            for (let i = 0; i < options.length; i++) {
                const option = document.createElement("option");
                option.text = options[i].msg;
                option.value = options[i].value
                party_time.appendChild(option);
            }
        }

    </script>
</x-app-layout>