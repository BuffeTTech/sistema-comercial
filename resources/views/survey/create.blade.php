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
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive px-4">
                            <form method="POST" action="{{ route('recommendation.store', ['buffet'=>$buffet->slug]) }}">
                                @csrf
                                <div class="form-group">
                                    <label for="content">Pergunta</label>
                                    <textarea class="form-control" id="content" rows="3" name="content"></textarea>
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
                                <x-input-error :messages="$errors->get('content')" class="mt-2" />
                                <button class="btn btn-primary" type="submit">Cadastrar Recomendação</button>
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