<div>
    <script>
        function confirmAction(action, message) {
            Swal.fire({
                text: message,
                icon: 'warning',
                showDenyButton: true,
                confirmButtonText: 'Ya',
                denyButtonText: 'Batal',
                customClass: {
                    denyButton: 'bg-primaryColor text-white px-24 py-2 rounded-md ml-2',
                    confirmButton: 'bg-white border-2 border-dangerColor text-dangerColor px-24 py-2 rounded-md mr-2',
                    popup: 'flex flex-col items-center'
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    action();
                }
            });
        }
    </script>
</div>
