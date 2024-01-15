@props(['value'])

<p {{ $attributes->merge(['class' => 'mt-1 text-sm text-gray-500 dark:text-gray-400']) }}>
    {{ $value ?? $slot }}    
</p>