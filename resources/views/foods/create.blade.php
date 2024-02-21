@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Comidas', 'subtitle'=>'Criar Pacote'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Criar Pacote de Alimentação</h6>
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive px-4">
                            <form method="POST" action="{{ route('food.store', ['buffet'=>$buffet->slug]) }}" enctype="multipart/form-data" id="form">
                                @csrf
                                <div class="form-group">
                                    <label for="name_food" class="form-control-label">Nome do Pacote</label>
                                    <input class="form-control" type="text" placeholder="Insira o nome do Pacote" id="name_food" name="name_food" value="{{ old('name_food') }}">
                                    <x-input-error :messages="$errors->get('name_food')" class="mt-2" />
                                </div>

                                <div class="form-group">
                                    <label for="slug" class="form-control-label">Slug</label>
                                    <input class="form-control" type="slug" placeholder="pacote-sensacao" id="slug" name="slug" value="{{ old('slug') }}">
                                    <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                                    <x-input-helper :value="'O nome unico relativo a URL do seu Pacote.'" class="mt-2" />
                                </div>

                                <div class="form-group">
                                    <label for="food_description" class="form-control-label">Descrição das Comidas</label>
                                    <textarea class="form-control textarea-container" id="food_description" rows="3" name="food_description" placeholder="Descrição das comidas do pacote">{{ old('food_description') }}</textarea>
                                    <x-input-error :messages="$errors->get('food_description')" class="mt-2" />
                                </div>

                                <div class="form-group">
                                    <label for="beverages_description" class="form-control-label">Descrição das Bebidas</label>
                                    <textarea class="form-control textarea-container" id="beverages_description" rows="3" name="beverages_description" placeholder="Descrição das bebidas do pacote">{{ old('beverages_description') }}</textarea>
                                    <x-input-error :messages="$errors->get('beverages_description')" class="mt-2" />
                                </div>

                                <div class="form-group">
                                    <label for="price" class="form-control-label">Preço do Pacote</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">R$</span>
                                        <input class="form-control" type="number" step="0.1" id="price" name="price" required aria-label="Preço" placeholder="Preço" value="{{old('price')}}">
                                    </div>
                                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                </div>

                                <h6>Selecione as imagens</h6>
                                <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                                @for($i=0; $i<$configurations->max_food_photos; $i++)
                                    
                                    <div class="form-group">
                                        <input type="file" class="form-control" id="foods_photo{{ $i+1 }}" name="foods_photo[]" accept="image/*">
                                    </div>
                                @endfor

                                <button class="btn btn-primary" type="submit">Cadastrar Pacote</button>
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
        
        const textarea = document.querySelectorAll(".textarea-container")
        textarea.forEach(element => {
            ClassicEditor
                .create(element)
                .catch(error => {
                    console.error(error);
                });
        });
    });
</script>