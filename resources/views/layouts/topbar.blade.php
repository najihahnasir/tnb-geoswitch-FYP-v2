<!-- ============================================================== -->
<!-- Topbar header - style you can find in pages.scss -->
<!-- ============================================================== -->
<header class="topbar">
    <nav class="navbar top-navbar navbar-expand-md navbar-dark">
        <div class="navbar-header">
            <!-- This is for the sidebar toggle which is visible on mobile only -->
            <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)">
                <i class="ri-close-line fs-6 ri-menu-2-line" style="color: #026670;"></i>
            </a>
            <!-- ============================================================== -->
            <!-- Logo -->
            <!-- ============================================================== -->
            <div class="navbar-brand" style="background-color: #ffff;">
                <a href="#" class="logo">
                    <!-- Logo icon -->
                    <b class="logo-icon">
                        <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                        <!-- Dark Logo icon -->
                        {{-- <img src="{{ asset('assets/images/logo-icon.png') }}" alt="homepage" class="dark-logo" /> --}}
                        <!-- Light Logo icon -->
                        {{-- <img src="{{ asset('assets/images/logo-light-icon.png') }}" alt="homepage" class="light-logo" /> --}}
                    </b>
                    <!--End Logo icon -->
                    <!-- Logo text -->
                    <span class="logo-text">
                        <!-- dark Logo text -->
                        <!-- <img  src="{{ asset('')  }}" width="200px" height="50px" alt="homepage" class="dark-logo" /> -->
                        <img src="{{ asset('images/tnb-logo.png')  }}" alt="Logo" width="80px" height="80px" class="light-logo responsive-center-logo-topbar" alt="homepage">
                        {{-- <img style="margin-left:20%;" src="{{ asset('assets/images/logo-psoh-3.png') }}"  width="170px" height="60px" class="light-logo" alt="homepage" /> --}}
                    </span>
                </a>
                <a class="sidebartoggler d-none d-md-block" href="javascript:void(0)" data-sidebartype="mini-sidebar">
                    <i class="mdi mdi-toggle-switch mdi-toggle-switch-off fs-6"></i>
                </a>
            </div>
            <!-- ============================================================== -->
            <!-- End Logo -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Toggle which is visible on mobile only -->
            <!-- ============================================================== -->
            <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i data-feather="more-horizontal" class="feather-sm" style="color: #026670;"></i>
            </a>
        </div>
        <!-- ============================================================== -->
        <!-- End Logo -->
        <!-- ============================================================== 026670-->
        <div class="navbar-collapse collapse" id="navbarSupportedContent" style="background-color:#026670">
            <!-- ============================================================== -->
            <!-- toggle and nav items -->
            <!-- ============================================================== -->
            <ul class="navbar-nav me-auto">
                <!-- <li class="nav-item d-none d-md-block">
                    <a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)" data-sidebartype="mini-sidebar">
                        <i class="mdi mdi-menu font-24"></i>
                    </a>
                </li> -->
                <!-- ============================================================== -->
                <!-- Search -->
                <!-- ============================================================== -->
                {{-- <li class="nav-item search-box">
                    <a class="nav-link waves-effect waves-dark" href="javascript:void(0)">
                        <div class="d-flex align-items-center">
                            <i data-feather="search" class="feather-sm me-1"></i>
                            <div class="ms-1 d-none d-sm-block">
                                <span>Search</span>
                            </div>
                        </div>
                    </a>
                    <form class="app-search position-absolute">
                        <input type="text" class="form-control" placeholder="Search &amp; enter">
                        <a class="srh-btn">
                            <i data-feather="x" class="feather-sm"></i>
                        </a>
                    </form>
                </li> --}}
            </ul>
            <!-- ============================================================== -->
            <!-- Right side toggle and nav items -->
            <!-- ============================================================== -->
            <ul class="navbar-nav">
                <!-- ============================================================== -->
                <!-- Messages -->
                <!-- ============================================================== -->
                <li class="nav-item dropdown">
                </li>
                <!-- ============================================================== -->
                <!-- End Messages -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Comment -->
                <!-- ============================================================== -->
                <li class="nav-item dropdown border-end">
                    <a class="nav-link dropdown-toggle waves-effect waves-dark position-relative" href="" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i data-feather="bell"></i>
                        <!-- Notification badge -->
                        <!-- <span class="badge rounded-pill bg-danger noti">3</span> -->
                    </a>
                    <div class="dropdown-menu dropdown-menu-end mailbox dropdown-menu-animate-up">
                        <ul class="list-style-none">
                            <li>
                                <div class="drop-title bg-light-orange text-dark">
                                    <h4 class="mb-0 m-t-5">3 Notifikasi</h4>
                                    <!-- <span class="fw-light">Baharu</span> -->
                                </div>
                            </li>
                            <li>
                                <div class="message-center notifications position-relative" style="height:230px;">
                                    <!-- Message -->
                                    <a href="#" class="message-item d-flex align-items-center border-bottom px-3 py-2">
                                        <span class="btn btn-light-primary text-primary btn-circle">
                                            <i class="ri-feedback-line fill-white"></i>
                                        </span>
                                        <div class="w-75 d-inline-block v-middle ps-3">
                                            <h5 class="message-title mb-0 mt-1 fs-3 font-weight-medium">Notification Title</h5>
                                            <span class="fs-2 text-nowrap d-block time text-truncate fw-normal text-muted mt-1">Notification Summary</span>
                                            <span class="fs-2 text-nowrap d-block subtext text-muted">9:30 AM</span>
                                        </div>
                                    </a>
                                </div>
                            </li>
                            <li>
                                <a class="nav-link text-center mt-2 mb-1 text-dark" href="#">
                                    <strong>Lihat Semua Notifikasi</strong>
                                    <i data-feather="chevron-right" class="feather-sm"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <!-- ============================================================== -->
                <!-- End Comment -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- User profile and search -->
                <!-- ============================================================== -->
                {{-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle waves-effect waves-dark pro-pic" href="" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="{{ asset('assets/images/users/2.jpg') }}" alt="user" class="rounded-circle" width="40">
                        <span class="ms-1 font-weight-medium d-none d-sm-inline-block">USER NAME <i data-feather="chevron-down" class="feather-sm"></i></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end user-dd animated flipInY">
                        <div class="d-flex no-block align-items-center p-3 bg-light-orange text-dark mb-2">
                            <div class=""><img src="{{ asset('assets/images/users/5.jpg') }}" alt="user" class="rounded-circle" width="60"></div>
                            <div class="ms-2">
                                <h4 class="mb-0 text-dark">USER NAME</h4>
                                <p class=" mb-0">user@example.com</p>
                            </div>
                        </div>
                        <a class="dropdown-item" href="#"><i data-feather="user" class="feather-sm text-info me-1 ms-1"></i>
                            Profil Saya</a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item"><i data-feather="log-out" class="feather-sm text-danger me-1 ms-1"></i> Keluar</a>
                    </div>
                </li> --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle waves-effect waves-dark pro-pic" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="{{ Auth::user()->profile_image ? asset(Auth::user()->profile_image) : asset('assets/images/users/default.jpg') }}" 
                             alt="user" class="rounded-circle" width="40">
                        <span class="ms-1 font-weight-medium d-none d-sm-inline-block">
                            {{ Auth::user()->name }} 
                            <i data-feather="chevron-down" class="feather-sm"></i>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end user-dd animated flipInY">
                        <div class="d-flex no-block align-items-center p-3 bg-light-orange text-dark mb-2">
                            <div class="">
                                <img src="{{ Auth::user()->profile_image ? asset(Auth::user()->profile_image) : asset('assets/images/users/default.jpg') }}" 
                                     alt="user" class="rounded-circle" width="60">
                            </div>
                            <div class="ms-2">
                                <h4 class="mb-0 text-dark">{{ Auth::user()->name }}</h4>
                                <p class="mb-0">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <a class="dropdown-item" href="{{ route('profile') }}">
                            <i data-feather="user" class="feather-sm text-info me-1 ms-1"></i> Profil Saya
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('logout') }}" class="dropdown-item"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i data-feather="log-out" class="feather-sm text-danger me-1 ms-1"></i> Keluar
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
                
                <!-- ============================================================== -->
                <!-- User profile and search -->
                <!-- ============================================================== -->
            </ul>
        </div>
    </nav>
</header>
<!-- ============================================================== -->
<!-- End Topbar header -->
<!-- ============================================================== -->