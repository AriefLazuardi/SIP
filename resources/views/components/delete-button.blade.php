<form id="delete-form-{{ $id }}" action="{{ $action }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="button" 
            onclick="confirmDelete(() => document.getElementById('delete-form-{{ $id }}').submit(), '{{ $message ?? 'Apakah Anda yakin ingin menghapus data ini?' }}')" 
            class="bg-dangerColor text-baseColor w-20 h-9 py-1 rounded-md text-xs  flex items-center justify-center">
            <span class="material-icons text-sm -translate-x-1">
                delete
            </span>
        {{ $slot }}
    </button>
</form>

@include('components.delete-alert')
