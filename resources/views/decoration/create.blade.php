<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold mb-4">Criar Decoração</h1>
                    <div>
                        <form method="POST" action="{{ route('decoration.store', ['buffet'=>$buffet->slug]) }}" enctype="multipart/form-data">
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
                            <x-input-error :messages="$errors->get('decorations_photos_generic')" class="mt-2" />
                            @for($i=0; $i<$configurations->max_decoration_photos; $i++)
                                <div>
                                    <x-input-label for="decoration_photos{{ $i+1 }}" :value="__('Inserir Imagem')" />
                                    <x-text-input id="decoration_photos{{ $i+1 }}" class="block mt-1 w-full" accept="image/png, image/gif, image/jpeg" type="file" name="decoration_photos[]" :value="old('decoration_photos{{ $i+1 }}')" required autofocus />
                                    <x-input-error :messages="$errors->get('decoration_photos[{{ $i }}]')" class="mt-2" />
                                </div> 
                            @endfor

                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button class="ms-4">
                                    {{ __('Adcionar Decoração') }}
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
        document.addEventListener('DOMContentLoaded', (event) => {
        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });
        });
    </script>

</x-app-layout>