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
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive px-4">
                            <form method="POST" action="{{ route('employee.store', ['buffet'=>$buffet->slug]) }}" id="form">
                                @csrf
                                <div class="form-group">
                                    <label for="name" class="form-control-label">Nome</label>
                                    <input class="form-control" type="text" placeholder="Insira seu Nome" id="name" name="name" value="{{ old('name') }}">
                                </div>

                                <div class="form-group">
                                    <label for="email" class="form-control-label">Email</label>
                                    <input class="form-control" type="email" placeholder="joao@example.com" id="email" name="email" value="{{ old('email') }}">
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="document_type" id="cpf" value="CPF">
                                    <label class="custom-control-label" for="cpf">CPF</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="document_type" id="cnpj" value="CNPJ">
                                    <label class="custom-control-label" for="cnpj">CNPJ</label>
                                </div>
                                    
                                <div class="form-group">
                                    <label for="document" class="form-control-label">Documento</label>
                                    <input class="form-control" type="text" placeholder="Insira o CPF/CNPJ" id="document" name="document" value="{{ old('document') }}">
                                    <span class="text-sm text-red-600 dark:text-red-400 space-y-1 document-error" id="document-error"></span>
                                </div>

                                <div class="form-group">
                                    <label for="phone1" class="form-control-label">Telefone</label>
                                    <input class="form-control phone" type="text" placeholder="(XX) XXXXX-XXXX" id="phone1" name="phone1" value="{{ old('phone1') }}">
                                </div>
                                    
                                <div class="form-group">
                                    <label for="role" class="form-control-label">Cargo</label>
                                    <select name="role" id="role" class="form-control" >
                                        @php
                                            $slug = $buffet_subscription->subscription->slug;
                                        @endphp
                                        @foreach($roles as $role)
                                            <option 
                                                {{ old('role') == $role['name'] ? "selected" : "" }}
                                                value="{{ $role->name }}">
                                                    {{ ucwords(explode($slug.'.', $role->name)[1]) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button class="btn btn-primary" type="submit">Cadastrar Funcionário</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
    <script>
        const doc = document.querySelector("#document")
        const doc_error = document.querySelector("#document-error")
        const form = document.querySelector("#form")

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