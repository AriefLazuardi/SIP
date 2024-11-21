@props(['options' => [], 'selected' => [], 'disabled' => false, 'placeholder' => 'Pilih disini', 'multiple' => false])

<select 
    {{ $disabled ? 'disabled' : '' }} 
    {!! $attributes->merge(['class' => 'border-baseColor focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) !!}
    {{ $multiple ? 'multiple' : '' }}
>
    <option value="" disabled {{ $selected === null ? 'selected' : '' }}>{{ $placeholder }}</option>
    @foreach($options as $value => $label)
        <option value="{{ $value }}" 
            {{ $multiple 
                ? (in_array($value, (array)$selected) ? 'selected' : '')
                : ($value == $selected ? 'selected' : '') 
            }}
        >
            {{ $label }}
        </option>
    @endforeach
</select>