<x-app-layout>
    @include('layouts.header_general')

    <script src="https://cdn.ckeditor.com/ckeditor5/37.0.1/classic/ckeditor.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                    @foreach ($errors->all() as $error)
                    {{ $error }}
                    @endforeach
                    @endif

                    <form class="w-full max-w-lg" action="{{ route('packages.store') }}" method="POST" enctype="multipart/form-data" id="form">

                        @csrf

                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full px-3 mb-6 md:mb-0">
                                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="name_package">
                                    Tema principal
                                </label>
                                <input required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="name_package" type="text" placeholder="Nome do Pacote" name="name_package" value="{{old('name_package')}}">
                            </div>
                        </div>
                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full px-3 mb-6 md:mb-0">
                                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="slug">
                                    Slug
                                </label>
                                <input required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="slug" type="text" placeholder="nome-com-hífens" name="slug" value="{{old('slug')}}">
                            </div>
                        </div>
                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full  px-3 mb-6 md:mb-0">
                                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="qnt_invited">
                                    Descrição
                                </label>
                                <textarea name="food_description" id="food_description" cols="30" rows="5" placeholder="Descrição das Comidas">{{old('food_description')}}</textarea>
                            </div>
                        </div>
                        {{-- <input type="file" name="images[]" id="" accept="image/png, image/gif, image/jpeg" required/> --}}
                        <button type="submit" class="bg-amber-300 hover:bg-amber-500 text-black font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                            Criar Decoração
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- <script>
        const form = document.querySelector("#form")
        const name_package = document.querySelector("#name_package")

        form.addEventListener('submit', async function(e) {
            e.preventDefault()
            const userConfirmed = await confirm(`Deseja criar o pacote ${name_package.value}?`)

            if (userConfirmed) {
                this.submit();
            } else {
                error("Ocorreu um erro!")
            }
        })
        ClassicEditor
            .create( document.querySelector( '#food_description' ) )
            .catch( error => {
                console.error( error );
            } );
        ClassicEditor
            .create( document.querySelector( '#beverages_description' ) )
            .catch( error => {
                console.error( error );
            } );
    </script> --}}
</x-app-layout>