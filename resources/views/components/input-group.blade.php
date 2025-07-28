@props(['label', 'name'])

<div class="w-full mt-4">
    <x-input-label :for="$name" :value="$label" />
    <x-text-input :name="$name" :id="$name" type="number" step="any" class="block w-full" required />
    <x-input-error :messages="$errors->get($name)" />
</div>
