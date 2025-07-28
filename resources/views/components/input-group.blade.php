@props(['label', 'name', 'type', 'value'])

<div class="w-full mt-4">
    <x-input-label :for="$name" :value="$label" />
    <x-text-input :name="$name" :id="$name" :type="$type" class="block w-full" :value="$value" required />
    <x-input-error :messages="$errors->get($name)" />
</div>
