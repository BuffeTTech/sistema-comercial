<x-app-layout>

    <script src="https://cdn.ckeditor.com/ckeditor5/37.0.1/classic/ckeditor.js"></script>

    <h1>Atualizar Pergunta</h1>
    <div>
        <form method="POST" action="{{ route('survey.update', ['buffet'=>$buffet->slug, 'survey'=>$survey->id]) }}" enctype="multipart/form-data" id="form">
            @method('put')
            @csrf

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full  px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-s font-bold mb-2" for="qnt_invited">
                        Pergunta
                    </label>
                    <textarea name="question" id="question" cols="40" rows="10" class="height-500 width-500" placeholder="Insira a questão">{{old('question')}}</textarea>
                </div>
            </div>

            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full  px-3 mb-6 md:mb-0">                            
                    <label for="question_type" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Tipo de pergunta</label>
                    <select name="question_type" id="question_type" required>
                        <option value="invalid" selected disabled>Selecione um formato disponível</option>
                        @foreach( App\Enums\QuestionType::array() as $key => $value )
                            <option value="{{$value}}">{{$key}}</option>
                        @endforeach
                    </select>
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="status">
                </div>
            </div>


            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="ms-4">
                    Atualizar Pergunta 
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        const form = document.querySelector("#form")

        form.addEventListener('submit', async function(e) {
            e.preventDefault()
            const userConfirmed = await confirm(`Deseja ataulizar esta pergunta?`)

            if (userConfirmed) {
                this.submit();
            } else {
                error("Ocorreu um erro!")
            }
        })
        ClassicEditor
            .create( document.querySelector('#question') )
            .catch( error => {
                console.error( error );
            } );
    </script> 
    
</x-app-layout>