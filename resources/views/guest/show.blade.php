<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 float-left" style="width: 50%; border-right: 3px solid #000000;">
                    <div class="bg-gray-50 border-b-2 border-gray-200">
                        <p><strong>Nome do Convidado:</strong> {{ $guest->name }}</p><br>
                        <p><strong>CPF:</strong> {{ $guest->document }}</p><br>
                        <p><strong>Idade:</strong> {{ $guest->age }}</p><br>
                        <p><strong>Status:</strong><x-status.guest_status :status="$guest->status" /></p><br>
            </div>
        </div>
    </div>
</x-app-layout>