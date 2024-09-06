@props(['value'])

<p {{ $attributes->merge(['class' => 'form-text']) }}>
    {{ $value ?? $slot }}    
</p>