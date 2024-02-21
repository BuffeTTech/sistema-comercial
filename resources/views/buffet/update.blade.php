@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Comidas', 'subtitle'=>'Criar Pacote'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Editar Buffet</h6>
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive px-4">
                            <form method="POST" action="{{ route('buffet.update', ['buffet'=>$buffet->slug]) }}" enctype="multipart/form-data">
                                @csrf
                                @method('put')

                                <div>
                                    <h2>Dados do Buffet</h2>

                                    <div class="form-group">
                                        <label for="trading_name" class="form-control-label">Nome Comercial*</label>
                                        <input class="form-control" type="text" placeholder="Buffet Alegria" id="trading_name" name="trading_name" value="{{old('trading_name') ?? $buffet->trading_name }}">
                                        <x-input-error :messages="$errors->get('trading_name')" class="mt-2" />
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="email_buffet" class="form-control-label">Email Comercial*</label>
                                        <textarea class="form-control textarea-container" id="email_buffet" rows="3" name="email_buffet" placeholder="buffetalegria@example.com">{{old('email_buffet') ?? $buffet->email_buffet}}</textarea>
                                        <x-input-error :messages="$errors->get('email_buffet')" class="mt-2" />
                                    </div>

                                    <div class="form-group">
                                        <label for="document_buffet" class="form-control-label">Documento do Buffet*</label>
                                        <textarea class="form-control textarea-container" id="document_buffet" rows="3" name="document_buffet" placeholder="CNPJ">{{old('document_buffet') ?? $buffet->document_buffet}}</textarea>
                                        <x-input-error :messages="$errors->get('document_buffet')" class="mt-2" />
                                    </div>

                                    <div class="form-group">
                                        <label for="slug" class="form-control-label">Slug</label>
                                        <input class="form-control" type="slug" placeholder="buffet-alegria" id="slug" name="slug" value="{{old('slug') ?? $buffet->slug}}">
                                        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                                        <x-input-helper :value="'O nome unico relativo a URL do seu Pacote.'" class="mt-2" />
                                    </div>

                                </div>

                                <div>
                                    <h2>Meios de Contato</h2>

                                    <div class="form-group">
                                        <label for="street" class="form-control-label">CEP</label>
                                        <input class="form-control" type="zipcode" placeholder="12345-678" id="zipcode" name="zipcode" value="{{old('zipcode') ?? $buffet->buffet_address->zipcode}}">
                                        <x-input-error :messages="$errors->get('zipcode')" class="mt-2" />
                                        <x-input-helper :value="'O nome unico relativo a URL do seu Pacote.'" class="mt-2" />
                                    </div>

                                    <div class="form-group">
                                        <label for="street" class="form-control-label">Logradouro</label>
                                        <input class="form-control" type="street" placeholder="Rua Confete" id="street" name="street" value="{{old('street') ?? $buffet->buffet_address->street}}">
                                        <x-input-error :messages="$errors->get('street')" class="mt-2" />
                                        <x-input-helper :value="'O nome unico relativo a URL do seu Pacote.'" class="mt-2" />
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="neighborhood" class="form-control-label">Bairro</label>
                                        <input class="form-control" type="neighborhood" placeholder="Bairro Alegria" id="neighborhood" name="neighborhood" value="{{old('neighborhood') ?? $buffet->buffet_address->neighborhood}}">
                                        <x-input-error :messages="$errors->get('neighborhood')" class="mt-2" />
                                        <x-input-helper :value="'O nome unico relativo a URL do seu Pacote.'" class="mt-2" />
                                    </div>

                                    <div class="form-group">
                                        <label for="state" class="form-control-label">Estado</label>
                                        <input class="form-control" type="state" placeholder="São Paulo" id="state" name="state" value="{{old('state') ?? $buffet->buffet_address->state}}">
                                        <x-input-error :messages="$errors->get('state')" class="mt-2" />
                                        <x-input-helper :value="'O nome unico relativo a URL do seu Pacote.'" class="mt-2" />
                                    </div>

                                    <div class="form-group">
                                        <label for="city" class="form-control-label">Cidade</label>
                                        <input class="form-control" type="city" placeholder="Campinas"  id="city" name="city" value="{{old('city') ?? $buffet->buffet_address->city}}">
                                        <x-input-error :messages="$errors->get('city')" class="mt-2" />
                                        <x-input-helper :value="'O nome unico relativo a URL do seu Pacote.'" class="mt-2" />
                                    </div>

                                    <div class="form-group">
                                        <label for="number" class="form-control-label">Número</label>
                                        <div class="input-group mb-3">
                                            <input class="form-control" type="number" step="1" id="number" name="number" required aria-label="Número" placeholder="123" value="{{old('number') ?? $buffet->buffet_address->number}}">
                                        </div>
                                        <x-input-error :messages="$errors->get('number')" class="mt-2" />
                                    </div>

                                    <div class="form-group">
                                        <label for="complement" class="form-control-label">Complemento</label>
                                        <textarea class="form-control textarea-container" id="complement" rows="3" name="complement" placeholder="">{{old('complement') ?? $buffet->buffet_address->complement}}</textarea>
                                        <x-input-error :messages="$errors->get('complement')" class="mt-2" />
                                    </div>
                                    <input type="hidden" name="country" value="Brazil">
                                    <div class="form-group">
                                        <label for="phone1_buffet" class="form-control-label">Telefone 1</label>
                                        <input class="form-control" type="phone1_buffet" placeholder="(XX) XXXXX-XXXX"  id="phone1_buffet" name="phone1_buffet" value="{{old('phone1_buffet') ?? $buffet->buffet_phone1->number ?? null}}">
                                        <x-input-error :messages="$errors->get('phone1_buffet')" class="mt-2" />
                                        <x-input-helper :value="'O nome unico relativo a URL do seu Pacote.'" class="mt-2" />
                                    </div>

                                    <div class="form-group">
                                        <label for="phone2_buffet" class="form-control-label">Telefone</label>
                                        <input class="form-control" type="phone2_buffet" placeholder="(XX) XXXXX-XXXX"  id="phone2_buffet" name="phone2_buffet" value="{{old('phone2_buffet') ?? $buffet->buffet_phone2->number ?? null}}">
                                        <x-input-error :messages="$errors->get('phone2_buffet')" class="mt-2" />
                                        <x-input-helper :value="'O nome unico relativo a URL do seu Pacote.'" class="mt-2" />
                                    </div>

                                    <button class="btn btn-primary" type="submit">Atualizar Buffet</button>
                                </form>
                                <div>
                                    <h2>Infomações Adicionais</h2>                            
                                    <form method="POST" action="{{ route('buffet.update_logo', ['buffet'=>$buffet->slug]) }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('put')
                                    <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                                    <div class="form-group">
                                        <input type="file" class="form-control" id="buffet_logo" name="photo" accept="image/*"  onchange="this.form.submit()" style="display: none">
                                            <label for="buffet_logo">
                                            @if($buffet->logo_id)
                                                <img src="{{asset('storage/buffets'.$buffet->logo->file_path)}}" alt="">
                                            @else
                                                <p>Para inserir imagem, clique aqui</p>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            </div>  
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