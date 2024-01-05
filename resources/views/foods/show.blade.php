<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 float-left" style="width: 50%; border-right: 3px solid #000000;">
                    <div class="bg-gray-50 border-b-2 border-gray-200">
                        <p><strong>Nome do pacote:</strong> {{ $food->name_food }}</p><br>
                        <p><strong>Slug:</strong> {{ $food->slug }}</p><br>
                        <p><strong>Preço:</strong> {{ $food->price }}</p><br>
                        @php
                        $class_active = "p-1.5 text-xs font-medium uppercase tracking-wider text-green-800 bg-green-200 rounded-lg bg-opacity-50";
                        $class_unactive = 'p-1.5 text-xs font-medium uppercase tracking-wider text-red-800 bg-red-200 rounded-lg bg-opacity-50';
                        @endphp
                        <p><strong>Status:</strong>
                            <form action="{{ route('food.change_status', ['buffet' => $buffet, 'food' => $food['slug']]) }}" method="post" class="inline">
                                @csrf
                                @method('patch')

                                <label for="status" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"></label>
                                <select name="status" id="status" required onchange="this.form.submit()">
                                    @foreach( App\Enums\FoodStatus::array() as $key=>$status )
                                        <option value="{{$status}}" {{ $food['status'] == $status ? 'selected' : ""}}>{{$key}}</option>
                                    @endforeach
                                    <!-- <option value="invalid2"  disabled>Nenhum horario disponivel neste dia, tente novamente!</option> -->
                                </select>
                            </form>
                        </p><br>
                        <p><strong>Descricao das comidas:</strong></p>
                        {!! $food->food_description !!}
                        <br>
                        <br>
                        <p><strong>Descricao das bebidas:</strong></p>
                        {!! $food->beverages_description !!}
                    </div>
                    <br><br>

                    <div class="flex items-center ml-auto float-down">
                        <a href="{{ route('food.edit', ['food'=>$food->slug, 'buffet'=>$buffet]) }}" class="bg-amber-300 hover:bg-amber-500 text-black font-bold py-2 px-4 rounded">
                            <div class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4">
                                Editar
                            </div>
                        </a>
                    </div>
                </div>

                <div class="p-6 text-gray-900 float-right" style="width: 50%;">
                    <!-- Imagens -->
                    <img src="{{ asset('storage/foods'. $foods_photo[0]->file_path) }}" alt="{{ $foods_photo[0]->file_name }}"> 
                    <img src="{{ asset('storage/foods'. $foods_photo[1]->file_path) }}" alt="{{ $foods_photo[1]->file_name }}">     
                </div>

                {{-- <div class="p-6 text-gray-900 float-right" style="width: 50%;">
                    <!-- Imagens -->
                    <img src="{{asset('storage/foods/'.$food->photo_1)}}" alt="foto1">
                    <img src="{{asset('storage/foods/'.$food->photo_2)}}" alt="foto2">
                    <img src="{{asset('storage/foods/'.$food->photo_3)}}" alt="foto3">
                </div> --}}
            </div>
        </div>
    </div>
</x-app-layout>