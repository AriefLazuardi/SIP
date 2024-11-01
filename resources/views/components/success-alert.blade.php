<div x-data="{
    showAlert() {
        Swal.fire({
            text: '{{ $message ?? 'Operasi berhasil dilakukan.' }}',
            icon: '{{ $icon ?? 'success' }}',
            confirmButtonText: '{{ $confirmButtonText ?? 'OK' }}',
            customClass: {
                confirmButton: 'bg-primaryColor border-0 text-white px-4 py-2 rounded-md w-96'
            },
            buttonsStyling: false,
        });
    }
}" 
    x-init="showAlert()">
</div>
