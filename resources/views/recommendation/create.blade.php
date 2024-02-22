@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Recomendações', 'subtitle'=>'Criar Recomendação'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Recomendações de festas</h6>
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive px-4">
                            <form method="POST" action="{{ route('recommendation.store', ['buffet'=>$buffet->slug]) }}" id="form">
                                @csrf
                                <div class="form-group">
                                    <label for="content">Conteúdo</label>
                                    <textarea class="form-control" id="content" rows="3" name="content"></textarea>
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

<script src="https://cdn.ckeditor.com/ckeditor5/37.0.1/classic/ckeditor.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
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
            .create(document.querySelector('#content'))
            .catch(error => {
                console.error(error);
            });
    });
</script>