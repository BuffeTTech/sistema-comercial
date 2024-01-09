<x-app-layout>

    <script src="https://cdn.ckeditor.com/ckeditor5/37.0.1/classic/ckeditor.js"></script>

    <h1>Editar Pacote</h1>
    <div>
        <form method="POST" action="{{ route('decoration.update', ['buffet'=>$buffet->slug,'decoration'=>$decoration->slug]) }}">
            @csrf
            @method('put')
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Name -->
            <div>
                <x-input-label for="main_theme" :value="__('Tema da Decoração')" />
                <x-text-input id="main_theme" class="block mt-1 w-full" type="text" name="main_theme" :value="$decoration->main_theme" required autofocus autocomplete="main_theme" />
                <x-input-error :messages="$errors->get('main_theme')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="slug" :value="__('Slug')" />
                <x-text-input id="slug" class="block mt-1 w-full" type="text" name="slug" :value="$decoration->slug" required autofocus autocomplete="slug" />
                <x-input-error :messages="$errors->get('slug')" class="mt-2" />
            </div>

            {{-- <div>
                <x-input-label for="" :value="__('Inserir Imagem')" />
                <x-text-input id="" class="block mt-1 w-full" type="file" name="" :value="old('')" required autofocus autocomplete="" />
                <x-input-error :messages="$errors->get('')" class="mt-2" />
            </div> --}}

            <div>
                <x-input-label for="description" :value="__('Descrição da Decoração: ')" />
                <textarea name="description" id="description" cols="40" rows="10" class="height-500 width-500" placeholder="Descrição">{{ html_entity_decode(old('description') ?? $decoration->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div> 

            <div>
                <x-input-label for="price" :value="__('Preço')" />
                <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="$decoration->price" required autofocus autocomplete="price" />
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="ms-4">
                    {{ __('Update') }}
                </x-primary-button>
            </div>
        </form>
    </div>

            <style>
                .input_file {
                    display: none;
                }
            </style>

            <h2><strong>Imagens:</strong></h2>
            <h3>Clique na imagem para alterar</h3>

            <div class="images bg-yellow-100">
                <form action="{{ route('decoration.update_photo', ['buffet' => $buffet->slug, 'decoration' => $decoration['slug'], 'decoration_photos' => $decoration_photos[0]->id]) }}" method="post" enctype="multipart/form-data" >
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="photo_id" value="1">
                    <input type="file" name="photo" id="photo_1" class="input_file" required onchange="this.form.submit()">
                    <label for="photo_1">
                        <img src="{{asset('storage/decorations'.$decoration_photos[0]->file_path)}}" alt="{{ $decoration_photos[0]->file_name }}">
                    </label>
                </form>
                <form action="{{ route('decoration.update_photo', ['buffet' => $buffet->slug, 'decoration' => $decoration['slug'], 'decoration_photos' => $decoration_photos[1]->id]) }}" method="post" enctype="multipart/form-data" >
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="photo_id" value="2">
                    <input type="file" name="photo" id="photo_2" class="input_file" required onchange="this.form.submit()">
                    <label for="photo_2">
                        <img src="{{asset('storage/decorations'.$decoration_photos[1]->file_path)}}" alt="{{ $decoration_photos[1]->file_name }}">
                    </label>
                </form>
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
        <a href="{{ route('decoration.show', ['decoration'=>$decoration->slug, 'buffet'=>$buffet->slug]) }}" class="font-bold text-blue-500 hover:underline"> 
                <div class="ms-4">
                    Back
                <div>
            </a>
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