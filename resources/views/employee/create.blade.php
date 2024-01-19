<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                <h1 class="text-3xl font-bold mb-4">Criar Funcionário</h1>
                <div>
                    <form method="POST" action="{{ route('employee.store', ['buffet'=>$buffet->slug]) }}" id="form">
                        @csrf

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Nome')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="document_type" :value="__('Documento')" />
                            <select name="document_type" id="document_type">
                                <option value="CPF">CPF</option>
                                <option value="CNPJ">CNPJ</option>
                            </select>
                            <x-input-error :messages="$errors->get('document_type')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="document" :value="__('Documento')" />
                            <x-text-input id="document" class="block mt-1 w-full" type="text" name="document" :value="old('document')" required autofocus autocomplete="document" />
                            <x-input-error :messages="$errors->get('document')" class="mt-2"/>
                            <span class="text-sm text-red-600 dark:text-red-400 space-y-1" id="document-error"></span>
                        </div>

                        <div>
                            <x-input-label for="phone1" :value="__('Telefone')" />
                            <x-text-input id="phone1" class="block mt-1 w-full phone" type="text" name="phone1" :value="old('phone1')" required autofocus autocomplete="phone1" />
                            <x-input-error :messages="$errors->get('phone1')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="role" :value="__('Cargo')" />
                            <select name="role" id="role">
                                @php
                                    $slug = $buffet_subscription->subscription->slug;
                                @endphp
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ ucwords(explode($slug.'.', $role->name)[1]) }}</option>
                                    (string $separator, string $string, int $limit = PHP_INT_MAX)
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>


                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4" id="button">
                                {{ __('Register') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const doc = document.querySelector("#document")
        const doc_type = document.querySelector("#document_type")
        const doc_error = document.querySelector("#document-error")
        const form = document.querySelector("#form")
        const phones = document.querySelectorAll(".phone")
        phones.forEach(phone => {
            phone.addEventListener('input', (e)=>{
                e.target.value = replacePhone(e.target.value);
                return;
            })
        });
        //const button = document.querySelector("#button");

        form.addEventListener('submit', async function (e) {
            e.preventDefault()
            if(doc_type.value === 'CPF') {
                const cpf_valid = validarCPF(doc.value)
                if(!cpf_valid) {
                    error('Documento inválido')
                    return
                }
            }
            if(doc_type.value === "CNPJ") {
                const cnpj_valid = validarCNPJ(doc.value)
                if(!cnpj_valid) {
                    error('Documento inválido')
                    return
                }
            }

            const userConfirmed = await confirm(`Deseja cadastrar este funcionário?`)

            if (userConfirmed) {
                this.submit();
            } else {
                error("Ocorreu um erro!")
                return;
            }
        })

        doc.addEventListener('input', (e)=>{
            if(doc_type.value === 'CPF') {
                e.target.value = replaceCPF(e.target.value);
                return;
            }
            if(doc_type.value === "CNPJ") {
                e.target.value = replaceCNPJ(e.target.value);
                return;
            }
        })

        doc.addEventListener('focusout', (e)=>{
            if(doc_type.value === 'CPF') {
                const cpf_valid = validarCPF(doc.value)
                if(!cpf_valid) {
                    //button.disabled = true;
                    doc_error.innerHTML = "Documento inválido"
                    return
                }
                doc_error.innerHTML = ""
                //button.disabled = false;
                return;
            }
            if(doc_type.value === "CNPJ") {
                const cnpj_valid = validarCNPJ(doc.value)
                if(!cnpj_valid) {
                    //button.disabled = true;
                    doc_error.innerHTML = "Documento inválido"
                    return
                }
                doc_error.innerHTML = ""
                //button.disabled = false;
                return;
            }
        })

        doc_type.addEventListener('change', (e)=>{
            doc.value = ""
        })
    </script>
</x-app-layout>
