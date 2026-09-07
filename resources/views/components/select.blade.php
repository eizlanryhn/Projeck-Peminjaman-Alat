@props([
    'name',
    'label' => null,
    'opsi' => [],
    'value' => null,
    'keyValue' => 'key',
    'keyLabel' => 'label',
    'placeholder' => null,
])
<div class="mb-3">
    @if ($label)
        <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    @endif
    <select class="form-select @error($name) is-invalid @enderror" id="{{ $name }}" name="{{ $name }}" {{ $attributes }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($opsi as $pilihan)
            @php
                $nilai = is_array($pilihan) ? $pilihan[$keyValue] : $pilihan->{$keyValue};
                $teks  = is_array($pilihan) ? $pilihan[$keyLabel] : $pilihan->{$keyLabel};
            @endphp
            <option value="{{ $nilai }}"
                {{ (string) old($name, $value) === (string) $nilai ? 'selected' : '' }}>
                {{ $teks }}
            </option>
        @endforeach
    </select>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
