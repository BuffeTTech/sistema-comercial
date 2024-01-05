<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 float-left" style="width: 50%; border-right: 3px solid #000000;">
                    <div class="bg-gray-50 border-b-2 border-gray-200">
                        <p><strong>Nome da Decoração:</strong> {{ $decoration->main_theme }}</p><br>
                        <p><strong>Slug:</strong> {{ $decoration->slug }}</p><br>
                        <p><strong>Descrição:</strong></p>
                        {!! $decoration->description !!}
                        <br>
                        <p><strong>Preço:</strong> {{ $decoration->price }}</p><br>
                        @php
                        $class_active = "p-1.5 text-xs font-medium uppercase tracking-wider text-green-800 bg-green-200 rounded-lg bg-opacity-50";
                        $class_unactive = 'p-1.5 text-xs font-medium uppercase tracking-wider text-red-800 bg-red-200 rounded-lg bg-opacity-50';
                        @endphp
                        <p><strong>Status:</strong><span class="{{ $decoration->status == 1 ? $class_active : $class_unactive }}">{{ $decoration->status == 1 ? "Ativado" : "Desativado" }}</span></p><br>
                    </div>
                    <br><br>

                    <div class="flex items-center ml-auto float-down">
                        <a href="{{ route('decoration.edit', ['buffet'=>$buffet->slug, 'decoration'=>$decoration->slug]) }}" class="bg-amber-300 hover:bg-amber-500 text-black font-bold py-2 px-4 rounded">
                            <div class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4">
                                Editar
                            </div>
                        </a>
                    </div>
                    <form action="{{ route('decoration.destroy', ['buffet'=>$buffet->slug, 'decoration'=>$decoration->slug]) }}" method="post" class="inline form">
                        @csrf
                        @method("delete")
                        <button type="submit" title="Deletar '{{$decoration->main_theme}}'" class="bg-red-600 hover:bg-red-800 text-white font-bold py-2 px-4 rounded inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4">Excluir</button>
                    </form>
                {{-- <div class="p-6 text-gray-900 float-right" style="width: 50%;">
                    <!-- Imagens -->
                    <img src="{{asset('storage/decorations/'.$decoration->photo_1)}}" alt="foto1">
                    <img src="{{asset('storage/decorations/'.$decoration->photo_2)}}" alt="foto2">
                    <img src="{{asset('storage/decorations/'.$decoration->photo_3)}}" alt="foto3">
                </div> --}}
            </div>
        </div>
    </div>
</x-app-layout>