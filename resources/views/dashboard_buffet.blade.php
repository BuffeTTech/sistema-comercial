<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Buffets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1>Buffet Dashboard</h1>
                    <iframe src="{{ route('booking.calendar', ['buffet'=>$buffet->slug]) }}" frameborder="1" width="1000px" height="700px"></iframe>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            async function execute_questions() {
                const csrf = document.querySelector('meta[name="csrf-token"]').content
                const data = await axios.get('{{ route("api.bookings.get_questions_by_user_id", ["buffet"=>$buffet->slug, "user_id"=>auth()->user()->hashed_id]) }}', {
                    headers: {
                        'X-CSRF-TOKEN': csrf
                    }
                })
        
                console.log(data.data)

                if(data.data.length == 0) return;
    
                const questions = data.data.questions.map((question, index)=>{
                    console.log(question)
                    if(question.question_type == "M") {
                        return `
                            <div>
                                <p><strong>${question.question}</strong></p>
                                <div>
                                    <input required name="rows[q-${question.id}]" type="radio" id="q-${question.id}-1" value="0-25%">
                                    <label for="q-${question.id}-1">0-25%</label>
                                </div>
                                <div>
                                    <input name="rows[q-${question.id}]" type="radio" id="q-${question.id}-2" value="0-25%">
                                    <label for="q-${question.id}-2">26-50%</label>
                                </div>
                                <div>
                                    <input name="rows[q-${question.id}]" type="radio" id="q-${question.id}-3" value="26-50%">
                                    <label for="q-${question.id}-3">51-75%</label>
                                </div>
                                <div>
                                    <input name="rows[q-${question.id}]" type="radio" id="q-${question.id}-4" value="76-100%">
                                    <label for="q-${question.id}-4">76-100%</label>
                                </div>
                            </div>
                        `
                    } else {
                        return `
                            <div>
                                <label for="q-${question.id}"><strong>${question.question}</strong></label>
                                <br>
                                <textarea required id="q-${question.id}" name="rows[q-${question.id}]"></textarea>
                            </div>
                        `
                    }
                })
                console.log(questions)
                const booking = data.data.data.booking
                
                const data_modal = {
                        title: "Pesquisa de satisfação",
                        content: `
                            <form action="{{ route('survey.answer_question', ['buffet'=>$buffet->slug]) }}" method="POST">
                                <p class="font-size-20px"><strong>Aniversariante ${booking.name_birthdayperson}</strong></p>
                                <br>
                                @csrf
                                ${questions.join('<br>')}
                                <input type="hidden" value="${booking.id}" name="booking_id">
                                <br>
                                <button type="submit" class="bg-amber-300 hover:bg-amber-500 text-black font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Enviar pesquisa</button>
                            </form>
                            `
                    }
                html(data_modal)
            }
            execute_questions()
        

        })
    </script>
</x-app-layout>