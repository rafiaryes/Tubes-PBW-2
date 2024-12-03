<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dapo Smart - Login</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom styles for this template-->

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link href="{{ asset('startbootstrap-sb-admin-2-gh-pages/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('css')
</head>

<body>

    <div class="" style="min-height: 100vh; max-width: 1660px; margin: auto;">
        {{ $slot }}
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('startbootstrap-sb-admin-2-gh-pages/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('startbootstrap-sb-admin-2-gh-pages/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('startbootstrap-sb-admin-2-gh-pages/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('startbootstrap-sb-admin-2-gh-pages/js/sb-admin-2.min.js') }}"></script>

    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')

    <script type="module">
        import { v4 as uuidv4 } from 'https://cdn.jsdelivr.net/npm/uuid@9.0.0/dist/esm-browser/index.js';

        function storeUUID() {
            const uuidKey = 'userUid';
            console.log(localStorage.getItem(uuidKey))
            if (!localStorage.getItem(uuidKey)) {
                const uuid = uuidv4();
                localStorage.setItem(uuidKey, uuid);
                console.log('Generated UUID and stored:', uuid);
            } else {
                console.log('UUID already exists:', localStorage.getItem(uuidKey));
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            storeUUID()
        });
    </script>

    <script>
        if (!localStorage.getItem('order_method')) {
            localStorage.setItem('order_method', 'dine_in');
        }

        if (!localStorage.getItem('payment_method')) {
            localStorage.setItem('payment_method', 'pay_in_casher');
        }

        function previewImage(event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                preview.src = e.target.result;
                preview.style.display = 'block'; // Show the preview image
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        }

        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault(); // Prevent the default form submission

            // Show SweetAlert confirmation
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
            }).then((result) => {
                // If the user confirms, submit the form
                if (result.isConfirmed) {

                    const form = $(this).closest('.delete-form');
                    console.log(form)
                    form.submit();
                }
            });
        });
    </script>
    @if (session('error2'))
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            Toast.fire({
                icon: 'error',
                title: '{{ session('error2') }}'
            })
        </script>
    @endif
    @if (session('success'))
        <script>
            const ToastSuccess = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            ToastSuccess.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            })
        </script>
    @endif
</body>

</html>
