<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Buffet') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-3xl font-bold mb-4">Atualizar buffet</h1>
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('buffet.update', ['buffet'=>$buffet->slug]) }}">
                        @csrf
                        @method('put')

                        <div>
                            <h2 class="text-xl font-semibold mb-3 mt-3">Dados do buffet</h2>
    
                            <div class="mt-2">
                                <x-input-label for="trading_name" :value="__('Nome comercial*')" class="dark:text-slate-100 text-slate-800"/>
                                <x-text-input placeholder="Insira o nome comercial do buffet" id="trading_name" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="text" name="trading_name" :value="$buffet->trading_name" required autofocus/>
                                <x-input-error :messages="$errors->get('trading_name')" class="mt-2" />
                            </div>
                            <div class="mt-2">
                                <x-input-label for="email_buffet" :value="__('E-mail comercial*')" class="dark:text-slate-100 text-slate-800"/>
                                <x-text-input placeholder="Insira o e-mail comercial do buffet" id="email_buffet" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="text" name="email_buffet" :value="$buffet->email" required autofocus />
                                <x-input-error :messages="$errors->get('email_buffet')" class="mt-2" />
                            </div>
                            <div class="mt-2">
                                <x-input-label for="document_buffet" :value="__('Documento do buffet*')" class="dark:text-slate-100 text-slate-800"/>
                                <x-text-input placeholder="Insira o documento do buffet" id="document_buffet" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="text" name="document_buffet" :value="$buffet->document" required autofocus/>
                                <x-input-error :messages="$errors->get('document_buffet')" class="mt-2" />
                                <span class="text-sm text-red-600 dark:text-red-400 space-y-1" id="document_buffet-error"></span>
                                <x-input-helper>Insira o CNPJ</x-helper-input>
                            </div>
                            <div class="mt-2">
                                <x-input-label for="slug" :value="__('Slug*')" class="dark:text-slate-100 text-slate-800"/>
                                <x-text-input placeholder="Insira o slug do buffet" id="slug" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="text" name="slug" :value="$buffet->slug" required autofocus />
                                <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                                <x-input-helper>Será o link de acesso do buffet, como por exemplo nossosistema.com/seu-buffet</x-helper-input>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold mb-3 mt-3">Meios de contato</h2>
                            <div class="mt-2">
                                <x-input-label for="zipcode" :value="__('CEP*')" class="dark:text-slate-100 text-slate-800"/>
                                <x-text-input placeholder="Insira o CEP do buffet" id="zipcode" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="text" name="zipcode" :value="$buffet->buffet_address->zipcode" required autofocus />
                                <x-input-error :messages="$errors->get('zipcode')" class="mt-2" />
                                <span class="text-sm text-red-600 dark:text-red-400 space-y-1" id="zipcode-error"></span>
                            </div>
                            <div class="mt-2">
                                <x-input-label for="street" :value="__('Logradouro*')" class="dark:text-slate-100 text-slate-800"/>
                                <x-text-input placeholder="Insira a rua do buffet" id="street" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="text" name="street" :value="$buffet->buffet_address->street" required autofocus readonly aria-readonly />
                                <x-input-error :messages="$errors->get('street')" class="mt-2" />
                            </div>
                            <div class="mt-2">
                                <x-input-label for="neighborhood" :value="__('Bairro*')" class="dark:text-slate-100 text-slate-800"/>
                                <x-text-input placeholder="Insira o bairro do buffet" id="neighborhood" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="text" name="neighborhood" :value="$buffet->buffet_address->neighborhood" required autofocus readonly aria-readonly />
                                <x-input-error :messages="$errors->get('neighborhood')" class="mt-2" />
                            </div>
                            <div class="mt-2">
                                <x-input-label for="state" :value="__('Estado*')" class="dark:text-slate-100 text-slate-800"/>
                                <x-text-input placeholder="Insira o estado do buffet" id="state" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="text" name="state" :value="$buffet->buffet_address->state" required autofocus readonly aria-readonly />
                                <x-input-error :messages="$errors->get('state')" class="mt-2" />
                            </div>
                            <div class="mt-2">
                                <x-input-label for="city" :value="__('Cidade*')" class="dark:text-slate-100 text-slate-800"/>
                                <x-text-input placeholder="Insira a cidade do buffet" id="city" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="text" name="city" :value="$buffet->buffet_address->city" required autofocus readonly aria-readonly />
                                <x-input-error :messages="$errors->get('city')" class="mt-2" />
                            </div>
                            <div class="mt-2">
                                <x-input-label for="number" :value="__('Número*')" class="dark:text-slate-100 text-slate-800"/>
                                <x-text-input placeholder="Insira o número do buffet" id="number" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="number" name="number" :value="$buffet->buffet_address->number" required autofocus />
                                <x-input-error :messages="$errors->get('number')" class="mt-2" />
                            </div>
                            <div class="mt-2">
                                <x-input-label for="complement" :value="__('Complemento')" class="dark:text-slate-100 text-slate-800"/>
                                <x-text-input placeholder="Insira o complemento do endereço do buffet" id="complement" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="text" name="complement" :value="$buffet->buffet_address->complement" autofocus />
                                <x-input-error :messages="$errors->get('complement')" class="mt-2" />
                            </div>
                            <input type="hidden" name="country" value="Brazil">
                            <div class="mt-2">
                                <x-input-label for="phone1_buffet" :value="__('Telefone 1*')" class="dark:text-slate-100 text-slate-800"/>
                                <x-text-input placeholder="Primeiro telefone de contato do buffet" id="phone1_buffet" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="text" name="phone1_buffet" :value="$buffet->buffet_phone1->number ?? ''" required autofocus />
                                <x-input-error :messages="$errors->get('phone1_buffet')" class="mt-2" />
                            </div>
    
                            <div class="mt-2">
                                <x-input-label for="phone2_buffet" :value="__('Telefone')" class="dark:text-slate-100 text-slate-800"/>
                                <x-text-input placeholder="Segundo telefone de contato do buffet" id="phone2_buffet" class="block mt-1 w-full dark:bg-slate-100 dark:text-slate-500" type="text" name="phone2_buffet" :value="$buffet->buffet_phone2->number ?? null" autofocus />
                                <x-input-error :messages="$errors->get('phone2_buffet')" class="mt-2" />
                            </div>
                        </div>


                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Atualizar Buffet') }}
                            </x-primary-button>
                        </div>
                    </form>
                    <div>
                        <style>
                            .input_file {
                                display: none;
                            }
                        </style>
                        <h2 class="text-xl font-semibold mb-3 mt-3">Informações especificas</h2>
                        <form action="{{ route('buffet.update_logo', ['buffet'=>$buffet->slug]) }}" method="post" enctype="multipart/form-data" >
                            @csrf
                            @method('put')
                            
                            <div class="mt-2">
                                <x-input-label for="buffet_logo" :value="__('Logo*')" class="dark:text-slate-100 text-slate-800 inline-block"/>
                                <input type="hidden" name="buffet_logo" value="1">
                                <input type="file" name="buffet_logo" id="buffet_logo" class="input_file" required onchange="this.form.submit()" accept="image/png, image/gif, image/jpeg">
                                <label for="buffet_logo">
                                    @if($buffet->logo_id)
                                        <img src="{{ asset('storage/buffets'.$buffet->logo->file_path) }}" alt="">
                                    @else
                                        <p>Inserir imagem, clique aqui</p>
                                    @endif
                                </label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>