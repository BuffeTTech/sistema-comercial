@extends('layouts.guest', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Confirmar Presença para festa de {{$booking->name_birthdayperson}}</h6>
                    </div>

                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive px-4">
                            <form method="POST" action="{{ route('guest.store', ['buffet'=>$buffet->slug, 'booking'=>$booking->hashed_id]) }}" enctype="multipart/form-data" id="form">
                                @csrf
                                <x-input-error :messages="$errors->get('error')" class="mt-2" />
                                <div id="form-rows">
                                    <div id="guest-0" class="form-row">
                                        <h2 class="text-xl font-bold mb-2">Convidado 1</h2>
                                        <div class="form-group">
                                            <label for="name0" class="form-control-label">Nome</label>
                                            <input class="form-control" type="text" placeholder="Nome" id="name0" name="rows[0][name]" value="{{ old('rows[0][name]') }}">
                                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                        </div>

                                        <div class="form-group">
                                            <label for="document0" class="form-control-label">CPF</label>
                                            <input class="form-control document" type="text" placeholder="CPF" id="document0" name="rows[0][document]" value="{{ old('rows[0][document]') }}">
                                            <span class="text-sm text-red-600 dark:text-red-400 space-y-1 document-error" id="document-error0"></span>
                                            <x-input-error :messages="$errors->get('document')" class="mt-2" />
                                        </div>

                                        <div class="form-group">
                                            <label for="age0" class="form-control-label">Idade</label>
                                            <input class="form-control" type="number" value="{{old('rows[0][age]')}}" id="age0" placeholder="Idade" name="rows[0][age]">
                                            <x-input-error :messages="$errors->get('age')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="clone-button" class="btn btn-success">+</button>
                                <button class="btn btn-primary" type="submit">Cadastrar Convidados</button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
    
    <script>
            const doc = document.querySelector("#document0")
            const doc_error = document.querySelector("#document-error0")
            const form = document.querySelector("#form")
            const clone_button = document.querySelector("#clone-button")
            clone_button.addEventListener('click', (e)=>{
                e.preventDefault()
                clonarCampos()
            })
    
            let contadorCampos = 0;
            function clonarCampos() {
                contadorCampos++;
                const camposOriginais = document.querySelector('.form-row');
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
                novoCampos.querySelector('h2').innerHTML = `Convidado ${contadorCampos+1}`
    
                document.getElementById('form-rows').appendChild(novoCampos);
    
                const documents = novoCampos.querySelector(".document")
                documents.id = documents.id.replace(/\d+/, contadorCampos)
                const documents_error = novoCampos.querySelector(".document-error")
                documents_error.innerHTML = ""
                
                documents.addEventListener('input', (e)=>{
                    e.target.value = replaceCPF(e.target.value);
                    return;
                })
    
                documents.addEventListener('focusout', (e)=>{
                    const cpf_valid = validarCPF(documents.value)
                    if(!cpf_valid) {
                        //button.disabled = true;
                        documents_error.innerHTML = "Documento inválido"
                        return
                    }
                    documents_error.innerHTML = ""
                    //button.disabled = false;
                    return;
                })
            }
    
            form.addEventListener('submit', async function (e) {
                // e.preventDefault()
                // const cpfs = document.querySelectorAll('.document')
    
                // let erro = false
                // cpfs.forEach(cpf => {
                //     const cpf_valid = validarCPF(cpf.value)
                //     if(!cpf_valid) {
                //         error("O CPF é invalido")
                //         erro = true
                //         return;
                //     }
                // });
                // if(erro) return
                // this.submit();
            })
    
            doc.addEventListener('input', (e)=>{
                e.target.value = replaceCPF(e.target.value);
                return;
            })
    
            doc.addEventListener('focusout', (e)=>{
                const cpf_valid = validarCPF(doc.value)
                if(!cpf_valid) {
                    //button.disabled = true;
                    doc_error.innerHTML = "Documento inválido"
                    return
                }
                doc_error.innerHTML = ""
                //button.disabled = false;
                return;
            })
    </script>
@endsection