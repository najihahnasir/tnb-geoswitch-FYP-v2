<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="wrappixel, admin dashboard, html css dashboard, web dashboard, bootstrap 5 admin, bootstrap 5, css3 dashboard, bootstrap 5 dashboard, nice admin bootstrap 5 dashboard, frontend, responsive bootstrap 5 admin template, ">
    <meta name="description" content="Nice is powerful and clean admin dashboard template, inpired from Google's Material Design">
    <meta name="robots" content="noindex,nofollow">
    <title>Login</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favcon.png')}}">
    <link href="{{ asset('css/style.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/libs/sweetalert2/dist/sweetalert2.css') }}">
</head>

<style>
    .bg-landing-page {
        background: url(../images/bg-2.png) no-repeat center center;
        background-size: cover;
    }

    .bg-login {
        background-color: rgba(255, 255, 255, 1);
        width: 400px;
        border-radius: 15px;
        box-shadow: 2px 3px #a3a3a3;
        padding: 20px;
    }

    .btn-login {
        background-color: #6bae44;
        border-radius: 30px;
        color: white;
        width: 100%;
    }

    .text-brown {
        color: #0c0c0c;
    }

    .text-register {
        font-size: 10px;
    }
</style>

<body class="bg-landing-page">
    <div class="container-fluid main-wrapper">
        <div class="preloader">
            <svg class="tea lds-ripple" width="37" height="48" viewbox="0 0 37 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M27.0819 17H3.02508C1.91076 17 1.01376 17.9059 1.0485 19.0197C1.15761 22.5177 1.49703 29.7374 2.5 34C4.07125 40.6778 7.18553 44.8868 8.44856 46.3845C8.79051 46.79 9.29799 47 9.82843 47H20.0218C20.639 47 21.2193 46.7159 21.5659 46.2052C22.6765 44.5687 25.2312 40.4282 27.5 34C28.9757 29.8188 29.084 22.4043 29.0441 18.9156C29.0319 17.8436 28.1539 17 27.0819 17Z" stroke="#233242" stroke-width="2"></path>
                <path d="M29 23.5C29 23.5 34.5 20.5 35.5 25.4999C36.0986 28.4926 34.2033 31.5383 32 32.8713C29.4555 34.4108 28 34 28 34" stroke="#233242" stroke-width="2"></path>
                <path id="teabag" fill="#233242" fill-rule="evenodd" clip-rule="evenodd" d="M16 25V17H14V25H12C10.3431 25 9 26.3431 9 28V34C9 35.6569 10.3431 37 12 37H18C19.6569 37 21 35.6569 21 34V28C21 26.3431 19.6569 25 18 25H16ZM11 28C11 27.4477 11.4477 27 12 27H18C18.5523 27 19 27.4477 19 28V34C19 34.5523 18.5523 35 18 35H12C11.4477 35 11 34.5523 11 34V28Z">
                </path>
                <path id="steamL" d="M17 1C17 1 17 4.5 14 6.5C11 8.5 11 12 11 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke="#233242"></path>
                <path id="steamR" d="M21 6C21 6 21 8.22727 19 9.5C17 10.7727 17 13 17 13" stroke="#233242" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </div>

        <div class="row justify-content-center auth-wrapper no-gutters d-flex no-block align-items-center">
            <div class="bg-login">
                <div class="d-flex justify-content-center logo-image-container">
                    <img src="{{ asset('images/tnb-logo.png') }}" alt="Logo" width="65px" height="90px">
                </div>
                <h4 class="pt-1 text-brown text-center" style="margin-bottom: 5px; padding: 0px;"><strong>LOG IN</strong></h4>
                <hr style="color:#69ff4f; height:4px; margin-top:0px; margin-bottom: 20px; padding: 0px;">

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form class="form-horizontal mt-3" method="POST" action="{{ route('login') }}"novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span style="color: red;">*</span></label>
                        <input type="email" name="email" class="form-control" style="border-color: #6bae44;" placeholder="Please enter your email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span style="color: red;">*</span></label>
                        <input type="password" name="password" class="form-control" style="border-color: #6bae44;" placeholder="Please enter your password" required>
                    </div>

                    <div class="form-group text-center mt-4">
                        <button class="btn btn-login waves-effect waves-light" type="submit">
                            Log In
                        </button>
                    </div>

                    <div class="form-group d-flex justify-content-center mt-3">
                        <div class="d-flex align-items-center">
                            <div class="ms-auto">
                                <a href="{{ url('/password-reset') }}" class="link text-register">Forgot Password</a>
                            </div>
                            <div class="ms-auto">
                                &nbsp;&nbsp; | &nbsp;&nbsp;
                            </div>
                            <div class="ms-auto">
                                <a href="{{ route('register') }}" class="link text-register">Register</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <p style="font-size: 10px;">All Rights Reserved. Developed for the TNB-FYP collaboration. Best viewed in Chrome and Mozilla, 1920x1080 resolution
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js')}}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    <script>
        $(".preloader").fadeOut();
    </script>
</body>

</html>