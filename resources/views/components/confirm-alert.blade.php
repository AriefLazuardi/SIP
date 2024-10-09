<div>
    <script>
        function confirmDelete(route, text) {
            Swal.fire({
                text: 'Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showDenyButton: true,
                confirmButtonText: 'Hapus',
                denyButtonText: 'Batal',
                customClass: {
                    denyButton: 'bg-primaryColor text-white px-24 py-2 rounded-md ml-2',
                    confirmButton: 'bg-white border-2 border-dangerColor text-dangerColor px-24 py-2 rounded-md mr-2',
                    popup: 'flex flex-col items-center'
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form with the given route
                    document.getElementById(route).submit();
                }
            });
        }
    </script>
</div>
