<x-guest-layout>
    <h1 class="text-white text-xl font-bold mb-4 ">Buffet {{ $buffet->trading_name }}</h1>
    <form method="POST" action="{{ route('register', ['buffet'=>$buffet->slug]) }}" id="form">
        @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Nome*')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Insira seu nome"/>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
        
                <!-- Email Address -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email*')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="email" placeholder="Insira seu e-mail"/>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
        
                <!-- Document -->
                <div class="mt-4">
                    <x-input-label for="document" :value="__('Documento*')" />
                    <x-text-input id="document" class="block mt-1 w-full" type="text" name="document" :value="old('document')" required autofocus autocomplete="document" placeholder="Insira seu CPF"/>
                    <x-input-error :messages="$errors->get('document')" class="mt-2" />
                    {{-- <x-input-helper>Insira o CPF</x-helper-input> --}}
                    <span class="text-sm text-red-600 dark:text-red-400 space-y-1" id="document-error"></span>
                </div>
        
                <div class="mt-4">
                    <x-input-label for="phone1" :value="__('Telefone*')" />
                    <x-text-input id="phone1" class="block mt-1 w-full" type="text" name="phone1" :value="old('phone1')" required autofocus autocomplete="phone1" placeholder="Insira seu telefone"/>
                    <x-input-error :messages="$errors->get('phone1')" class="mt-2" />
                </div>
        
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Senha')" />
        
                    <x-text-input id="password" class="block mt-1 w-full"
                                    type="password"
                                    placeholder="Insira sua senha"
                                    name="password"
                                    required autocomplete="new-password" />
        
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
        
                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirmação de senha')" />
        
                    <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                    type="password"
                                    placeholder="Insira a confirmação da senha"
                                    name="password_confirmation" required autocomplete="new-password" />
        
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login', ['buffet'=>$buffet->slug]) }}">
                {{ __('Já está cadastrado?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Cadastrar') }}
            </x-primary-button>
        </div>
    </form>
    <script>
        const doc = document.querySelector("#document")
        const doc_error = document.querySelector("#document-error")
        const form = document.querySelector("#form")
        const phone1 = document.querySelector("#phone1")

        form.addEventListener('submit', async function (e) {
            e.preventDefault()

            const document_valid = validarCPF(doc.value)
            if(!document_valid) {
                error("O documento é invalido")
                return;
            }

            if(document.querySelector("#password").value !== document.querySelector("#password_confirmation").value) {
                error("As senhas não são iguais")
                return;
            }

            this.submit();
        })



        doc.addEventListener('input', (e)=>{
            e.target.value = replaceCPF(e.target.value);
            return;
        })
        phone1.addEventListener('input', (e)=>{
            e.target.value = replacePhone(e.target.value);
            return;
        })

        doc.addEventListener('focusout', (e)=>{
            const cpf_valid = validarCPF(doc.value)
            if(!cpf_valid) {
                doc_error.innerHTML = "Documento inválido"
                return
            }
            doc_error.innerHTML = ""
            return;
        })
    </script>
</x-guest-layout>
