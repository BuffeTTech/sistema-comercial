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
                            <form method="POST" action="{{ route('decoration.store', ['buffet'=>$buffet->slug]) }}" enctype="multipart/form-data" id="form">
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
                                @if($configurations['max_decoration_photos'] !== null)
                                    @for($i=0; $i<$configurations->max_decoration_photos; $i++)
                                        <div class="form-group">
                                            <input type="file" class="form-control" id="decoration_photos{{ $i+1 }}" name="decoration_photos[]" accept="image/*">
                                        </div>
                                    @endfor
                                @else
                                    <div>
                                        <div id="photos_wrapper">
                                            <div class="form-group photo_row">
                                                {{-- <label for="foods_photo1"></label> --}}
                                                <input type="file" class="form-control" id="decoration_photos1" name="decoration_photos[]" accept="image/*">
                                            </div>
                                        </div>
                                        <button id="more-photos" class="btn btn-success">+</button>
                                    </div>
                                @endif

                                <button class="btn btn-primary" type="submit">Cadastrar Decoração</button>

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
            const userConfirmed = await confirm(`Deseja criar esta decoração?`)
    
            if (userConfirmed) {
                this.submit();
            } else {
                error("Ocorreu um erro!")
            }
        })
        document.addEventListener('DOMContentLoaded', (event) => {
            ClassicEditor
                .create(document.querySelector('#description'))
                .catch(error => {
                    console.error(error);
                });

            let contadorCampos = 1;
            const more = document.querySelector("#more-photos")
            if(more) {
                const container = document.querySelector("#photos_wrapper")
                more.addEventListener('click', (e)=>{
                    contadorCampos++;
                    e.preventDefault();
                    const camposOriginais = document.querySelector(".photo_row")
                    const novoCampos = camposOriginais.cloneNode(true);
        
                    novoCampos.querySelectorAll('input').forEach((input) => {
                        input.id = input.id.replace(/\d+/, contadorCampos);
                        input.name = input.name.replace(/\d+/, contadorCampos);
                        input.value = '';
                    });
        
                    novoCampos.querySelectorAll('label').forEach((label) => {
                        const novoFor = label.getAttribute('for').replace(/\d+/, contadorCampos);
                        label.setAttribute('for', novoFor);
                    });

                    novoCampos.id = novoCampos.id.replace(/\d+/, contadorCampos)
        
                    container.appendChild(novoCampos);
                })
            }
        });
    </script>
@endsection