@props(['message' => 'Operasi Gagal Dilakukan', 'icon' => 'error', 'confirmButtonText' => 'Coba Lagi', 'denyButtonText' => 'Batal'])

<div 
    x-data="{ 
        message: '{{ $message }}',
        icon: '{{ $icon }}',
        init() {
        console.log('Error Alert Init:', {
            message: this.message,
            icon: this.icon
        });
        this.showAlert();
        },
        showAlert() {
         console.log('Showing alert');
            Swal.fire({
                text: this.message,
                icon: this.icon,
                confirmButtonText: '{{ $confirmButtonText }}',
                denyButtonText: '{{ $denyButtonText }}',
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
    x-init="init">
</div>