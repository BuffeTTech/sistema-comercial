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
                            <form method="POST" action="{{ route('buffet.update', ['buffet'=>$buffet->slug]) }}" enctype="multipart/form-data" id="form">
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
                                        <input class="form-control" type="email" placeholder="buffet-alegria" id="email_buffet" name="email_buffet" value="{{old('email_buffet') ?? $buffet->email}}">
                                        <x-input-error :messages="$errors->get('email_buffet')" class="mt-2" />
                                    </div>

                                    <div class="form-group">
                                        <label for="document_buffet" class="form-control-label">Documento do Buffet*</label>
                                        <input class="form-control" type="text" placeholder="buffet-alegria" id="document_buffet" name="document_buffet" value="{{old('document_buffet') ?? $buffet->document}}">
                                        <x-input-error :messages="$errors->get('document_buffet')" class="mt-2" />
                                        <span class="text-sm text-danger space-y-1 document-error" id="document_buffet-error"></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="slug" class="form-control-label">Slug</label>
                                        <input class="form-control" type="text" placeholder="buffet-alegria" id="slug" name="slug" value="{{old('slug') ?? $buffet->slug}}">
                                        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                                        <x-input-helper :value="'O nome unico relativo a URL do seu buffet.'" class="mt-2" />
                                    </div>

                                </div>

                                <div>
                                    <h2>Meios de Contato</h2>

                                    <div class="form-group">
                                        <label for="street" class="form-control-label">CEP</label>
                                        <input class="form-control" type="text" placeholder="12345-678" id="zipcode" name="zipcode" value="{{old('zipcode') ?? $buffet->buffet_address->zipcode}}">
                                        <x-input-error :messages="$errors->get('zipcode')" class="mt-2" />
                                        <span class="text-sm text-danger space-y-1 document-error" id="zipcode-error"></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="street" class="form-control-label">Logradouro</label>
                                        <input class="form-control" type="text" placeholder="Rua Confete" id="street" name="street" value="{{old('street') ?? $buffet->buffet_address->street}}">
                                        <x-input-error :messages="$errors->get('street')" class="mt-2" />
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="neighborhood" class="form-control-label">Bairro</label>
                                        <input class="form-control" type="text" placeholder="Bairro Alegria" id="neighborhood" name="neighborhood" value="{{old('neighborhood') ?? $buffet->buffet_address->neighborhood}}">
                                        <x-input-error :messages="$errors->get('neighborhood')" class="mt-2" />
                                    </div>

                                    <div class="form-group">
                                        <label for="state" class="form-control-label">Estado</label>
                                        <input class="form-control" type="text" placeholder="São Paulo" id="state" name="state" value="{{old('state') ?? $buffet->buffet_address->state}}">
                                        <x-input-error :messages="$errors->get('state')" class="mt-2" />
                                    </div>

                                    <div class="form-group">
                                        <label for="city" class="form-control-label">Cidade</label>
                                        <input class="form-control" type="text" placeholder="Campinas"  id="city" name="city" value="{{old('city') ?? $buffet->buffet_address->city}}">
                                        <x-input-error :messages="$errors->get('city')" class="mt-2" />
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
                                        <input class="form-control" type="text" placeholder=""  id="complement" name="complement" value="{{old('complement') ?? $buffet->buffet_address->complement}}">
                                        <x-input-error :messages="$errors->get('complement')" class="mt-2" />
                                    </div>
                                    <input type="hidden" name="country" value="Brazil">
                                    <div class="form-group">
                                        <label for="phone1_buffet" class="form-control-label">Telefone 1</label>
                                        <input class="form-control" type="text" placeholder="(XX) XXXXX-XXXX"  id="phone1_buffet" name="phone1_buffet" value="{{old('phone1_buffet') ?? $buffet->buffet_phone1->number ?? null}}">
                                        <x-input-error :messages="$errors->get('phone1_buffet')" class="mt-2" />
                                    </div>

                                    <div class="form-group">
                                        <label for="phone2_buffet" class="form-control-label">Telefone</label>
                                        <input class="form-control" type="text" placeholder="(XX) XXXXX-XXXX"  id="phone2_buffet" name="phone2_buffet" value="{{old('phone2_buffet') ?? $buffet->buffet_phone2->number ?? null}}">
                                        <x-input-error :messages="$errors->get('phone2_buffet')" class="mt-2" />
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
    <script>
        const zipcode = document.querySelector('#zipcode');
        const street = document.querySelector('#street');
        const neighborhood = document.querySelector('#neighborhood');
        const state = document.querySelector('#state');
        const city = document.querySelector('#city');
        const number = document.querySelector('#number');
        const complement = document.querySelector('#complement');
        const zipcode_error = document.querySelector("#zipcode-error")
        const document_buffet = document.querySelector("#document_buffet")
        const doc_buffet_error = document.querySelector("#document_buffet-error")
        const form = document.querySelector("#form")
        
        document_buffet.addEventListener('input', (e)=>{
            e.target.value = replaceCNPJ(e.target.value);
            return;
        })

        document_buffet.addEventListener('focusout', (e)=>{
            const cnpj_valid = validarCNPJ(e.target.value)
            if(!cnpj_valid) {
                doc_buffet_error.innerHTML = "Documento inválido"
                return
            }
            doc_buffet_error.innerHTML = ""
            return;
        })

        zipcode.addEventListener('input', async (e) => {
            e.target.value = replaceCEP(e.target.value)
        })

        zipcode.addEventListener('focusout', async (e) => {
            try {
                // const onlyNumbers = /^[0-9]+$/;
                // const cepValid = /^[0-9]{8}$/;

                // if(!onlyNumbers.test(e.target.value) || !cepValid.test(e.target.value)) {
                //     console.log(onlyNumbers.test(e.target.value), cepValid.test(e.target.value))
                //     throw {cep_error: 'CEP inválido'}
                // }
                const cep = e.target.value.replace(/\D/g, '');

                const response = await fetch(`http://viacep.com.br/ws/${cep}/json`)

                const responseCep = await response.json()
                console.log(responseCep)

                if(responseCep?.erro) {
                    throw {cep_error: 'CEP inválido'}
                }
                zipcode_error.innerHTML = ""
                street.value = responseCep.logradouro
                neighborhood.value = responseCep.bairro
                state.value = responseCep.uf
                city.value = responseCep.localidade
                number.value = ""
                complement.value = ""

                // number.innerHTML = response
                // complement.innerHTML = response
                // country.innerHTML = response

            } catch(error) {
                street.value = ""
                neighborhood.value = ""
                state.value = ""
                city.value = ""
                complement.value = ""
                number.value = ""
                zipcode_error.innerHTML = "CEP Inválido"
                console.log(error)
            }
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const cnpj_valid = validarCNPJ(document_buffet.value);
            if (!cnpj_valid) {
                error('Documento inválido');
                return;
            }

            const userConfirmed = await confirm(`Deseja atualizar o buffet?`);

            if (userConfirmed) {
                // Envie o formulário manualmente
                this.form.submit();
            }
        });
    </script>
@endsection