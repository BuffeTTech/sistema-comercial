<x-app-layout>

    <script src="https://cdn.ckeditor.com/ckeditor5/37.0.1/classic/ckeditor.js"></script>

    <h1>Criar Pacote de Comida e Bebidas</h1>
    <div>
        <form method="POST" action="{{ route('food.store', ['buffet'=>$buffet->slug]) }}" enctype="multipart/form-data">
            @csrf

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Name -->
            <div>
                <x-input-label for="name_food" :value="__('Nome do Pacote')" />
                <x-text-input id="name_food" class="block mt-1 w-full" type="text" name="name_food" :value="old('name_food')" required autofocus autocomplete="name_food" />
                <x-input-error :messages="$errors->get('name_food')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="slug" :value="__('Slug')" />
                <x-text-input id="slug" class="block mt-1 w-full" type="text" name="slug" :value="old('slug')" required autofocus autocomplete="slug" />
                <x-input-error :messages="$errors->get('slug')" class="mt-2" />
            </div>

            

            <div class="mt-4">
                <x-input-label for="food_description" :value="__('Descrição das comidas')" />
                <textarea name="food_description" id="food_description" cols="40" rows="10" class="height-500 width-500" placeholder="Descrição das comidas">{{ old('food_description')}}</textarea>
                <x-input-error :messages="$errors->get('food_description')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="beverages_description" :value="__('Descrição das bebidas')" />
                <textarea name="beverages_description" id="beverages_description" cols="40" rows="10" class="height-500 width-500" placeholder="Descrição das comidas"> {{old('beverages_description') }}</textarea>
                <x-input-error :messages="$errors->get('beverages_description')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="price" :value="__('Preço do Pacote')" />
                <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="old('price')" required autofocus autocomplete="price" />
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="foods_photo1" :value="__('Inserir Imagem')" />
                <x-text-input id="foods_photo1" class="block mt-1 w-full" type="file" name="foods_photo[]" :value="old('foods_photo1')" required autofocus autocomplete="foods_photo1" />
                <x-input-error :messages="$errors->get('foods_photo[0]')" class="mt-2" />
            </div> 

            <div>
                <x-input-label for="foods_photo2" :value="__('Inserir Imagem')" />
                <x-text-input id="foods_photo2" class="block mt-1 w-full" type="file" name="foods_photo[]" :value="old('')" required autofocus autocomplete="" />
                <x-input-error :messages="$errors->get('foods_photo[1]')" class="mt-2" />
            </div> 


            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="ms-4">
                    Criar Pacote 
                </x-primary-button>
            </div>
        </form>
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