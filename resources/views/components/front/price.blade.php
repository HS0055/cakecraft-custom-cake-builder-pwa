@props(['amount', 'class' => ''])

@php
    $currency = settings(\App\Settings\CurrencySettings::class);
@endphp

<span {{ $attributes->merge(['class' => 'font-semibold ' . $class]) }}>
    {{ $currency->currency_symbol }}{{ number_format((float) $amount, 2) }}
</span>