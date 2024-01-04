<x-app-layout>
    <h1>Editar Pacote</h1>
    <div>
        <form method="POST" action="{{ route('food.update', ['buffet'=>$buffet->slug, 'food'=>$food->slug]) }}">
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
                <x-text-input id="food_description" class="block mt-1 w-full" type="food_description" name="food_description" :value="$food->food_description" required autocomplete="food_description" />
                <x-input-error :messages="$errors->get('food_description')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="beverages_description" :value="__('Descrição das bebidas')" />
                <x-text-input id="beverages_description" class="block mt-1 w-full" type="text" name="beverages_description" :value="$food->beverages_description" required autofocus autocomplete="beverages_description" />
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
    </div>
</x-app-layout>