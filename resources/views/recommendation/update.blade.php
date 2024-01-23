<x-app-layout>

    <script src="https://cdn.ckeditor.com/ckeditor5/37.0.1/classic/ckeditor.js"></script>

    <h1>Editar Pacote</h1>
    <div>
        <form method="POST" action="{{ route('recommendation.update', ['buffet'=>$buffet->slug,'recommendation'=>$recommendation]) }}">
            @csrf
            @method('put')
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Name -->
            <div>
                <x-input-label for="content" :value="__('Conteúdo')" />
                <x-text-input id="content" class="block mt-1 w-full" type="text" name="content" :value="$recommendation->content" required autofocus autocomplete="content" />
                <x-input-error :messages="$errors->get('content')" class="mt-2" />
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


        <div class="flex items-center justify-end mt-4">
        <a href="{{ route('recommendation.show', ['recommendation'=>$recommendation, 'buffet'=>$buffet->slug]) }}" class="font-bold text-blue-500 hover:underline"> 
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