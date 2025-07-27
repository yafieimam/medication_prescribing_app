<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Obat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if(isset($medicines['medicines']))
                    <ul>
                        @foreach($medicines['medicines'] as $obat)
                            <li>{{ $obat['id'] }} - {{ $obat['name'] }}</li>
                        @endforeach
                    </ul>
                @else
                    <p>Tidak ada data ditemukan.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>