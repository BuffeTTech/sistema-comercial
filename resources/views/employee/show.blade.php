<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 float-left" style="width: 50%; border-right: 3px solid #000000;">
                    <div class="bg-gray-50 border-b-2 border-gray-200">
                        <p><strong>Nome do Funcionário:</strong> {{ $employee->name }}</p><br>
                        <p><strong>Email:</strong> {{ $employee->email }}</p><br>
                        <p><strong>Cargo:</strong>{{ $employee->roles[0]->name ?? "" }}</p><br>
                        {{-- <p><strong>Status:</strong>
                            <form action="{{ route('employee.change_status', ['buffet' => $buffet, 'employee' => $employee['slug']]) }}" method="post" class="inline">
                                @csrf
                                @method('patch')
            
                                <label for="status" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"></label>
                                <select name="status" id="status" required onchange="this.form.submit()">
                                    @foreach( App\Enums\employeeStatus::array() as $key=>$status )
                                        <option value="{{$status}}" {{ $employee['status'] == $status ? 'selected' : ""}}>{{$key}}</option>
                                    @endforeach
                                    <!-- <option value="invalid2"  disabled>Nenhum horario disponivel neste dia, tente novamente!</option> -->
                                </select>
                            </form>
                        </p><br> --}}

                    <div class="flex items-center ml-auto float-down">
                        <a href="{{ route('employee.edit', ['buffet'=>$buffet->slug, 'employee'=>$employee->hashed_id]) }}" class="bg-amber-300 hover:bg-amber-500 text-black font-bold py-2 px-4 rounded">
                            <div class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4">
                                Editar
                            </div>
                        </a>
                    </div>

                   
            </div>
        </div>
    </div>
</x-app-layout>