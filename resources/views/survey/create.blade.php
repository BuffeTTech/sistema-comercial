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
                            <form method="POST" action="{{ route('survey.store', ['buffet'=>$buffet->slug]) }}" id="form">
                                @csrf
                                <div class="form-group">
                                    <label for="question" class="form-control-label">Pergunta</label>
                                    <textarea class="form-control" id="question" rows="3" name="question" required>{{old('question')}}</textarea>
                                    <x-input-error :messages="$errors->get('question')" class="mt-2" />
                                </div>
                                <div class="flex flex-wrap -mx-3 mb-6 form-group">
                                    <label class="form-control-label">Tipo de pergunta</label>
                                    <div class="w-full px-3 mb-6 md:mb-0">  
                                        <div>
                                            @foreach( App\Enums\QuestionType::array() as $key => $value )
                                            <div class="form-check">
                                                <input required class="form-check-input" type="radio" name="question_type" id="{{ $value }}" value="{{ $value }}" {{old('question') == $value ? 'checked' : ''}}>
                                                <label class="custom-control-label" for="{{ $value }}">{{ $key }}</label>
                                            </div>                  
                                            @endforeach
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('question_type')" class="mt-2" />
                                </div>                            
                                <button class="btn btn-primary" type="submit">Cadastrar Pergunta</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div> 
@endsection

<script>
    const form = document.querySelector("#form")

    form.addEventListener('submit', async function(e) {
        e.preventDefault()
        const userConfirmed = await confirm(`Deseja criar esta pergunta?`)

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