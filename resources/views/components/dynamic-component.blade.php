@props(['component'])

@php
    // Простая реализация для динамических компонентов
    $iconMap = [
        'list-bullet' => 'heroicon-o-list-bullet',
        'plus' => 'heroicon-o-plus',
        'calendar' => 'heroicon-o-calendar',
    ];
    
    $icon = str_replace('heroicon-o-', '', $component);
    $iconClass = $iconMap[$icon] ?? $component;
@endphp

@if($iconClass === 'heroicon-o-list-bullet')
    <svg {{ $attributes->merge(['class' => 'w-4 h-4']) }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
@elseif($iconClass === 'heroicon-o-plus')
    <svg {{ $attributes->merge(['class' => 'w-4 h-4']) }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
    </svg>
@elseif($iconClass === 'heroicon-o-calendar')
    <svg {{ $attributes->merge(['class' => 'w-4 h-4']) }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
    </svg>
@else
    {{-- Fallback --}}
    <div {{ $attributes->merge(['class' => 'w-4 h-4']) }}></div>
@endif