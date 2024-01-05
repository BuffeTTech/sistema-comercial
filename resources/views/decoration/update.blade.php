<x-app-layout>
    <h1>Editar Pacote</h1>
    <div>
        <form method="POST" action="{{ route('decoration.update', ['buffet'=>$buffet->slug,'decoration'=>$decoration]) }}">
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

            <div class="mt-4">
                <x-input-label for="description" :value="__('Descrição das Decorações')" />
                <x-text-input id="description" class="block mt-1 w-full" type="description" name="description" :value="$decoration->description" required autocomplete="description" />
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="price" :value="__('Preço')" />
                <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="$decoration->price" required autofocus autocomplete="price" />
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>

            <div>
                <label for="status" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"></label>
                <select name="status" id="status">
                    @foreach( App\Enums\DecorationStatus::array() as $key => $value )
                        <option value="{{$value}}" {{ $decoration->status == $value ? 'selected' : ""}}>{{$key}}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="ms-4">
                    {{ __('Update') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>