<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-bs-theme="dark" data-body-image="img-1" data-preloader="disable">


<head>

    <meta charset="utf-8" />
    <title>Sign In | Downtown</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Downtown" name="description" />
    <meta content="Bitzsol" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('/')}}assets/images/favicon.ico">

    <!-- Layout config Js -->
    <script src="{{asset('/')}}assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="{{asset('/')}}assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{asset('/')}}assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{asset('/')}}assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{asset('/')}}assets/css/custom.min.css" rel="stylesheet" type="text/css" />
    <link href="{{asset('/')}}assets/css/style.css" rel="stylesheet" type="text/css">
</head>

<body>

    <div class="auth-page-wrapper pt-5">
        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            <div>
                                <a href="index.html" class="d-inline-block auth-logo">
                                    <img src="{{asset('/')}}logo.svg" alt="" height="150">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                @yield('content')
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">&copy;
                                <script>document.write(new Date().getFullYear())</script> Downtown. Crafted with <i class="mdi mdi-heart text-danger"></i> by Bitzsol.com
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="{{asset('/')}}assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{asset('/')}}assets/libs/simplebar/simplebar.min.js"></script>
    <script src="{{asset('/')}}assets/libs/node-waves/waves.min.js"></script>
    <script src="{{asset('/')}}assets/libs/feather-icons/feather.min.js"></script>
    <script src="{{asset('/')}}assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="{{asset('/')}}assets/js/plugins.js"></script>

    <!-- particles js -->
    <script src="{{asset('/')}}assets/libs/particles.js/particles.js"></script>
    <!-- particles app js -->
    <script src="{{asset('/')}}assets/js/pages/particles.app.js"></script>
    <!-- password-addon init -->
    <script src="{{asset('/')}}assets/js/pages/password-addon.init.js"></script>
</body>


<!-- Mirrored from themesbrand.com/velzon/html/galaxy/auth-signin-basic.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 21 Jan 2024 10:49:51 GMT -->
</html>
