@props(['activo' => false])

<a {{ $attributes->class([
        'rounded-full px-4 py-2 text-sm font-medium transition',
        'bg-oro-500/15 text-oro-300'                             => $activo,
        'text-crema-300 hover:bg-oro-500/10 hover:text-crema-100' => ! $activo,
   ]) }}
   @if ($activo) aria-current="page" @endif>
    {{ $slot }}
</a>
