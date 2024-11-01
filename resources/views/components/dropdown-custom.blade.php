@props(['options' => [], 'selected' => null, 'disabled' => false, 'placeholder' => 'Pilih disini'])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-customColor focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) !!}>
    <option value="" disabled selected>{{ $placeholder }}</option> <!-- Opsi Pilihan -->
    @foreach($options as $value => $label)
        <option value="{{ $value }}" {{ $value == $selected ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
</select>