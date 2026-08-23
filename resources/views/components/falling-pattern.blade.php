@props([
    'speed' => '60s',
    'mask' => true,
])

<div {{ $attributes->merge(['class' => 'pointer-events-none absolute inset-0 overflow-hidden']) }}>
    <div class="falling-pattern absolute inset-0 transition-opacity duration-500"
         style="animation-duration: {{ $speed }};"></div>
    @if($mask)
        <div class="falling-pattern-overlay absolute inset-0"></div>
        <div class="absolute inset-0" style="background: radial-gradient(ellipse at 50% 40%, transparent 0%, rgb(3 7 18 / 0.6) 90%)"></div>
    @endif
</div>
