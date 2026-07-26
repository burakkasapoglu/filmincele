@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-700 bg-gray-800 text-white focus:border-rose-500 focus:ring-rose-500 rounded-lg shadow-sm placeholder-gray-500']) !!}>
