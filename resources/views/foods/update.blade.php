<x-app-layout>
    
    <script src="https://cdn.ckeditor.com/ckeditor5/37.0.1/classic/ckeditor.js"></script>

    <h1>Editar Pacote</h1>

    <div>
        <form method="POST" action="{{ route('food.update', ['buffet'=>$buffet->slug, 'food'=>$food->slug]) }}" enctype="multipart/form-data">
            @method('put')
            @csrf

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Name -->
            <div>
                <x-input-label for="name_food" :value="__('Nome do Pacote')" />
                <x-text-input id="name_food" class="block mt-1 w-full" type="text" name="name_food" value="{{ old('name_food') ?? $food->name_food}}" required autofocus autocomplete="name_food" />
                <x-input-error :messages="$errors->get('name_food')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="slug" :value="__('Slug')" />
                <x-text-input id="slug" class="block mt-1 w-full" type="text" name="slug" :value="$food->slug" required autofocus autocomplete="slug" />
                <x-input-error :messages="$errors->get('slug')" class="mt-2" />
            </div>

            {{-- <div>
                <x-input-label for="" :value="__('Inserir Imagem')" />
                <x-text-input id="" class="block mt-1 w-full" type="file" name="" :value="old('')" required autofocus autocomplete="" />
                <x-input-error :messages="$errors->get('')" class="mt-2" />
            </div> --}}

            <div class="mt-4">
                <x-input-label for="food_description" :value="__('Descrição das comidas')" />
                <textarea name="food_description" id="food_description" cols="40" rows="10" class="height-500 width-500" placeholder="Descrição das comidas">{{ html_entity_decode(old('food_description') ?? $food->food_description) }}</textarea>
                <x-input-error :messages="$errors->get('food_description')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="beverages_description" :value="__('Descrição das bebidas')" />
                <textarea name="beverages_description" id="beverages_description" cols="40" rows="10" class="height-500 width-500" placeholder="Descrição das comidas">{{ html_entity_decode(old('beverages_description') ?? $food->beverages_description) }}</textarea>
                <x-input-error :messages="$errors->get('beverages_description')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="price" :value="__('Preço do Pacote')" />
                <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="$food->price" required autofocus autocomplete="price" />
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>

           


            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="ms-4">
                    {{ __('Update') }}
                </x-primary-button>
            </div>
        </form>

        <style>
            .input_file {
                display: none;
            }
        </style>

        <h2><strong>Imagens:</strong></h2>
        <h3>Clique na imagem para alterar</h3>

        <div class="images bg-yellow-100">
            <form action="{{ route('food.update_photo', ['buffet' => $buffet->slug, 'food' => $food['slug'], 'foods_photo' => $foods_photo[0]->id]) }}" method="post" enctype="multipart/form-data" >
                @csrf
                @method('PATCH')
                <input type="hidden" name="photo_id" value="1">
                <input type="file" name="photo" id="photo_1" class="input_file" required onchange="this.form.submit()">
                <label for="photo_1">
                    <img src="{{asset('storage'.$foods_photo[0]->file_path)}}" alt="{{ $foods_photo[0]->file_name }}">
                </label>
            </form>
            <form action="{{ route('food.update_photo', ['buffet' => $buffet->slug, 'food' => $food['slug'], 'foods_photo' => $foods_photo[1]->id]) }}" method="post" enctype="multipart/form-data" >
                @csrf
                @method('PATCH')
                <input type="hidden" name="photo_id" value="2">
                <input type="file" name="photo" id="photo_2" class="input_file" required onchange="this.form.submit()">
                <label for="photo_2">
                    <img src="{{asset('storage'.$foods_photo[1]->file_path)}}" alt="{{ $foods_photo[1]->file_name }}">
                </label>
            </form>
        </div>
    </div>

    <div class="flex items-center justify-end mt-4">
       <a href="{{ route('food.show', ['food'=>$food->slug, 'buffet'=>$buffet->slug]) }}" class="font-bold text-blue-500 hover:underline""> 
            <div class="ms-4">
                Back
            <div>
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
        ClassicEditor
            .create(document.querySelector('#food_description'))
            .catch(error => {
                console.error(error);
            });
            ClassicEditor
            .create(document.querySelector('#beverages_description'))
            .catch(error => {
                console.error(error);
            });
        });
    </script>

</x-app-layout>