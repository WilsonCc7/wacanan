@props(['name' => '', 'class' => ''])

@php
    $initials = collect(explode(' ', trim((string) $name)))
        ->filter()
        ->take(2)
        ->map(fn ($word) => strtoupper(Str::substr($word, 0, 1)))
        ->implode('');
    if ($initials === '') {
        $initials = 'W';
    }
@endphp

<span class="inline-flex shrink-0 select-none items-center justify-center rounded-full bg-accent-50 font-semibold text-accent-700 ring-1 ring-inset ring-accent-200 {{ $class }}">
    {{ $initials }}
</span>
