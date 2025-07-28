<div {{ $attributes->merge(['class' => 'bg-white p-5 rounded shadow-sm']) }}>
    @isset($title)
        <h3 class="text-lg font-semibold text-gray-800 mb-3">{{ $title }}</h3>
    @endisset
    {{ $slot }}
</div>
