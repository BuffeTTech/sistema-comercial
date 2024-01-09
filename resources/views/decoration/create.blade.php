<x-app-layout>
    <script src="https://cdn.ckeditor.com/ckeditor5/37.0.1/classic/ckeditor.js"></script>
    <h1>Criar Decoração</h1>
    <div>
        <form method="POST" action="{{ route('decoration.store', ['buffet'=>$buffet]) }}">
            @csrf

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif


            <!-- Name -->
            <div>
                <x-input-label for="main_theme" :value="__('Tema Principal')" />
                <x-text-input id="main_theme" class="block mt-1 w-full" type="text" name="main_theme" :value="old('main_theme')" required autofocus autocomplete="main_theme" />
                <x-input-error :messages="$errors->get('main_theme')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="slug" :value="__('Slug')" />
                <x-text-input id="slug" class="block mt-1 w-full" type="text" name="slug" :value="old('slug')" required autofocus autocomplete="slug" />
                <x-input-error :messages="$errors->get('slug')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="description" :value="__('Descrição da Decoração: ')" />
                <textarea name="description" id="description" cols="40" rows="10" class="height-500 width-500" placeholder="Descrição">{{ old('description')}}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div> 

            {{-- <div class="mt-4">
                <x-input-label for="" :value="__('Inserir imagem')" />
                <x-text-input id="" class="block mt-1 w-full" type="text" name="" :value="old('')" required autocomplete="" />
                <x-input-error :messages="$errors->get('')" class="mt-2" />
            </div> --}}

            <div>
                <x-input-label for="price" :value="__('Preço do Pacote')" />
                <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="old('price')" required autofocus autocomplete="price" />
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>


            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="ms-4">
                    {{ __('Adcionar Decoração') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });
        });
    </script>

</x-app-layout>