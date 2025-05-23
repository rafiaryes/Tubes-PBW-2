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

    <!-- Custom styles for this template-->
    
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link href="{{ asset("startbootstrap-sb-admin-2-gh-pages/css/sb-admin-2.min.css") }}" rel="stylesheet">
    @stack('css')
</head>

<body class="bg-gradient-info">

    <div class="container">       
        {{ $slot }}
    </div>

    <!-- Bootstrap core JavaScript-->    
    <script src="{{ asset("startbootstrap-sb-admin-2-gh-pages/vendor/jquery/jquery.min.js") }}"></script>    
    <script src="{{ asset("startbootstrap-sb-admin-2-gh-pages/vendor/bootstrap/js/bootstrap.bundle.min.js") }}"></script>

    <!-- Core plugin JavaScript-->    
    <script src="{{ asset("startbootstrap-sb-admin-2-gh-pages/vendor/jquery-easing/jquery.easing.min.js") }}"></script>

    <!-- Custom scripts for all pages-->    
    <script src="{{ asset("startbootstrap-sb-admin-2-gh-pages/js/sb-admin-2.min.js") }}"></script>    

</body>


</html>