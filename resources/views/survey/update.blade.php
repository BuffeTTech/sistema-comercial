@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Perguntas', 'subtitle'=>'Criar Pergunta'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Pesquisa de satisfação</h6>
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive px-4">
                            <form method="POST" action="{{ route('survey.update', ['buffet'=>$buffet->slug, 'survey'=>$survey->hashed_id]) }}" id="form">
                                @csrf
                                @method('put')
                                <div class="form-group">
                                    <label for="question" class="form-control-label">Pergunta</label>
                                    <textarea class="form-control" id="question" rows="3" name="question" >{{old('question') ?? $survey->question}}</textarea>
                                    <x-input-error :messages="$errors->get('question')" class="mt-2" />
                                </div>
                                <div class="flex flex-wrap -mx-3 mb-6 form-group">
                                    <label class="form-control-label">Tipo de pergunta</label>
                                    <div class="w-full px-3 mb-6 md:mb-0">  
                                        <div>
                                            @foreach( App\Enums\QuestionType::array() as $key => $value )
                                            <div class="form-check">
                                                <input required class="form-check-input" type="radio" name="question_type" id="{{ $value }}" value="{{ $value }}" {{old('question_type') == $value || $survey->question_type == $value ? 'checked' : ''}}>
                                                <label class="custom-control-label" for="{{ $value }}">{{ $key }}</label>
                                            </div>                  
                                            @endforeach
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('question_type')" class="mt-2" />
                                </div>                                
                                <button class="btn btn-primary" type="submit">Atualizar Pergunta</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div> 
    <script src="https://cdn.ckeditor.com/ckeditor5/37.0.1/classic/ckeditor.js"></script>
    <script>
        const form = document.querySelector("#form")
    
        form.addEventListener('submit', async function(e) {
            e.preventDefault()
            const userConfirmed = await confirm(`Deseja criar esta pergunta?`)
    
            if (userConfirmed) {
                this.submit();
            }
        })
        ClassicEditor
            .create( document.querySelector('#question') )
            .catch( error => {
                console.error( error );
            } );
    </script>  
@endsection
