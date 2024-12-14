@props(['message' => 'Operasi berhasil dilakukan.', 'icon' => 'success', 'confirmButtonText' => 'OK'])

<div 
    x-data="{ 
        message: '{{ $message }}',
        icon: '{{ $icon }}',
        init() {
            this.showAlert();
        },
        showAlert() {
         console.log('Showing alert');
            Swal.fire({
                text: this.message,
                icon: this.icon,
                confirmButtonText: '{{ $confirmButtonText }}',
                customClass: {
                    confirmButton: 'bg-primaryColor border-0 text-white px-4 py-2 rounded-md w-96'
                },
                buttonsStyling: false,
            });
        }
    }" 
    x-init="init">
</div>