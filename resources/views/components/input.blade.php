@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => '',
    'hint' => null,
])
<div class="field-group">
    @if($label)
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->class(['input-error' => $errors->has($name)]) }}
    >
    @if($hint)
        <small class="field-hint muted">{{ $hint }}</small>
    @endif
    @error($name)
        <div class="field-error" role="alert">{{ $message }}</div>
    @enderror
</div>
