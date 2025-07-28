@if (session('success') || $errors->any())
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 5000)"
        x-show="show"
        x-transition
        class="fixed top-5 right-5 z-50 max-w-sm p-4 rounded shadow-lg text-white
               {{ session('success') ? 'bg-green-600' : 'bg-red-600' }}"
    >
        @if (session('success'))
            ✅ {{ session('success') }}
        @endif

        @if ($errors->any())
            <ul class="list-disc text-sm pl-4">
                @foreach ($errors->all() as $error)
                    <li>⚠️ {{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
