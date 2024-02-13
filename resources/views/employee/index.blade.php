<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Funcionários') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-auto">
                        <div>
                            <h1 class="inline-flex items-center border border-transparent text-lg leading-4 font-semi-bold">Listagem dos Funcionários</h1>
                            @if(count($employees) < $configurations['max_employees'])
                                <h2><a href="{{ route('employee.create', ['buffet'=>$buffet->slug]) }}">Criar funcionário</a></h2>
                            @endif
                        </div>
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b-2 border-gray-200">
                                <tr>
                                    <th class="p-3 text-sm font-semibold tracking-wide text-center">Nome</th>
                                    <th class="p-3 text-sm font-semibold tracking-wide text-center">Email</th>
                                    <th class="p-3 text-sm font-semibold tracking-wide text-center">Cargo</th>
                                    <th class="p-3 text-sm font-semibold tracking-wide text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @if($employees->total() === 0)
                                <tr>
                                    <td colspan="7" class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">Nenhum funcionário encontrado</td>
                                </tr>
                                @else   
                                    @foreach($employees->items() as $employee)
                                    <tr>
                                        <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                            <a href="{{ route('employee.show', ['buffet'=>$buffet->slug, 'employee'=>$employee->hashed_id]) }}" class="font-bold text-blue-500 hover:underline">
                                                {{ $employee->name }}
                                            </a>
                                        </td>
                                        <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $employee->email }}</td>
                                        <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">{{ $employee->roles[0]->name ?? ""}}</td>
                                        <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center"><x-status.user_status :status="$employee->status" /></td>

                                        <td class="p-3 text-sm text-gray-700 whitespace-nowrap text-center">
                                             @can('show employee')
                                            <a href="{{ route('employee.show', ['buffet'=>$buffet->slug, 'employee'=>$employee->hashed_id]) }}" title="Visualizar '{{$employee->name}}'">👁️</a>
                                            @endcan
                                            @if($employee->status !== \App\Enums\UserStatus::UNACTIVE->name)
                                                @can('update employee')
                                                    <a href="{{ route('employee.edit', ['buffet'=>$buffet->slug, 'employee'=>$employee->hashed_id]) }}" title="Editar '{{$employee->name}}'">✏️</a>
                                                @endcan
                                                @can('delete employee')
                                                    <form action="{{ route('employee.destroy', ['buffet'=>$buffet->slug, 'employee'=>$employee->hashed_id]) }}" method="post" class="inline">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" title="Deletar '{{ $employee->name }}'">❌</button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        {{ $employees->links('components.pagination') }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>