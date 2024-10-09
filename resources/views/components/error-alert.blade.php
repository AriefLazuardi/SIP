<div x-data="{
    showAlert() {
        Swal.fire({
            text: '{{ $message ?? 'Operasi Gagal Dilakukan' }}',
            icon: '{{ $icon ?? 'error' }}',
            confirmButtonText: '{{ $confirmButtonText ?? 'Coba Lagi' }}',
            denyButtonText: '{{ $denyButtonText ?? 'Batal' }}',
            showDenyButton: true,
            customClass: {
                confirmButton: 'bg-primaryColor text-white px-16 py-2 rounded-md border-0 mr-5 border-primaryColor',
                denyButton: 'bg-white text-dangerColor px-20 py-2 rounded-md border-dangerColor border-2',
            },
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            } else if (result.isDenied) {
                window.location.href = '{{ route(getDashboardRoute()) }}';
            }
        });
    }
}" 
x-init="showAlert()">
</div>
