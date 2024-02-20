@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Decorações', 'subtitle'=>'Criar Decoração'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Decorações das festas</h6>
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive px-4">
                            <form method="POST" action="{{ route('decoration.store', ['buffet'=>$buffet->slug]) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="main_theme" class="form-control-label">Tema</label>
                                    <input class="form-control" type="text" placeholder="Insira o nome do tema" id="main_theme" name="main_theme" value="{{ old('main_theme') }}">
                                    <x-input-error :messages="$errors->get('main_theme')" class="mt-2" />
                                </div>

                                <div class="form-group">
                                    <label for="slug" class="form-control-label">Slug</label>
                                    <input class="form-control" type="slug" placeholder="pacote-ben-10" id="slug" name="slug" value="{{ old('slug') }}">
                                    <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                                    <x-input-helper :value="'O nome unico relativo a URL do seu pacote de decoração.'" class="mt-2" />
                                </div>

                                <div class="form-group">
                                    <label for="description" class="form-control-label">Descrição</label>
                                    <textarea class="form-control" id="description" rows="3" name="description" placeholder="Descrição do pacote">{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                <div class="form-group">
                                    <label for="price" class="form-control-label">Preço</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">R$</span>
                                        <input class="form-control" type="number" step="0.1" id="price" name="price" required aria-label="Preço" placeholder="Preço" value="{{old('price')}}">
                                    </div>
                                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                </div>
                                    
                                <h6>Selecione as imagens</h6>
                                <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                                @for($i=0; $i<$configurations->max_decoration_photos; $i++)
                                    
                                    <div class="form-group">
                                        <input type="file" class="form-control" id="decoration_photos{{ $i+1 }}" name="decoration_photos[]" accept="image/*">
                                    </div>
                                @endfor

                                <button class="btn btn-primary" type="submit">Cadastrar Decoração</button>

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
    ClassicEditor
        .create(document.querySelector('#description'))
        .catch(error => {
            console.error(error);
        });
    });
</script>