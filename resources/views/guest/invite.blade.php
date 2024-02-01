<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold mb-4">Adicionar Convidado para a festa de {{$booking->name_birthdayperson}}</h1>
                    <div>
                        <form method="POST" action="{{ route('guest.store', ['buffet'=>$buffet->slug, 'booking'=>$booking->hashed_id]) }}" enctype="multipart/form-data" id="form">
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

                            <div>
                                <x-input-label for="document" :value="__('CPF')" />
                                <x-text-input id="document" class="block mt-1 w-full" type="text" name="document" :value="old('document')" required autofocus autocomplete="document" />
                                <x-input-error :messages="$errors->get('document')" class="mt-2" />
                                <span class="text-sm text-red-600 dark:text-red-400 space-y-1" id="document-error"></span>
                            </div>

                            <div>
                                <x-input-label for="age" :value="__('Idade')" />
                                <x-text-input id="age" class="block mt-1 w-full" type="number" name="age" :value="old('age')" required autofocus autocomplete="age" />
                                <x-input-error :messages="$errors->get('age')" class="mt-2" />
                            </div> 


                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button class="ms-4">
                                    {{ __('Adcionar Convidado') }}
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
        const doc = document.querySelector("#document")
        const doc_error = document.querySelector("#document-error")
        const form = document.querySelector("#form")
        //const button = document.querySelector("#button");

        form.addEventListener('submit', async function (e) {
            e.preventDefault()
            const cpf_valid = validarCPF(doc.value)
            if(!cpf_valid) {
                error('Documento inválido')
                return
            }

            const userConfirmed = await confirm(`Deseja convidar esta pessoa na festa?`)

            if (userConfirmed) {
                this.submit();
            } else {
                error("Ocorreu um erro!")
                return;
            }
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