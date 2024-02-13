<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold mb-4">Adicionar Convidado para a festa de {{$booking->name_birthdayperson}}</h1>
                    <div>
                        <form method="POST" action="{{ route('guest.store', ['buffet'=>$buffet->slug, 'booking'=>$booking->hashed_id]) }}" enctype="multipart/form-data" id="form">
                            <x-input-error :messages="$errors->get('message')" class="mt-2" />
                            @csrf

                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <x-input-error :messages="$errors->get('error')" class="mt-2" />

                            <div id="form-rows">
                                <div id="guest-0" class="form-row">
                                    <h2 class="text-xl font-bold mb-2">Convidado 1</h2>
                                    <div class="mb-3">
                                        <x-input-label for="name0" :value="__('Nome')" class="dark:text-slate-800" />
                                        <x-text-input id="name0" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" placeholder="Nome" type="text" name="rows[0][name]" :value="old('name')" required autofocus />
                                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                    </div>
        
                                    <div class="mb-3">
                                        <x-input-label for="document0" :value="__('CPF')" class="dark:text-slate-800"/>
                                        <x-text-input id="document0" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500 document" placeholder="CPF" type="text" name="rows[0][document]" :value="old('document')" required autofocus />
                                        <x-input-error :messages="$errors->get('document')" class="mt-2" />
                                        <span class="text-sm text-red-600 dark:text-red-400 space-y-1 document-error" id="document-error0"></span>
                                    </div>
        
                                    <div class="mb-3">
                                        <x-input-label for="age0" :value="__('Idade')" class="dark:text-slate-800"/>
                                        <x-text-input id="age0" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" placeholder="Idade" type="number" name="rows[0][age]" :value="old('age')" required autofocus />
                                        <x-input-error :messages="$errors->get('age')" class="mt-2" />
                                    </div> 
                                </div>
                            </div>

                            <button type="button" id="clone-button" style="width: 50px; height: 50px;" class="bg-amber-300 rounded-md text-xl mt-3">+</button>


                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button class="ms-4">
                                    {{ __('Adicionar Convidado') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.ckeditor.com/ckeditor5/37.0.1/classic/ckeditor.js"></script>


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
            e.preventDefault()
            const cpfs = document.querySelectorAll('.document')

            let erro = false
            cpfs.forEach(cpf => {
                const cpf_valid = validarCPF(cpf.value)
                if(!cpf_valid) {
                    error("O cpf é invalido")
                    erro = true
                    return;
                }
            });
            if(erro) return
            this.submit();
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

</x-app-layout>