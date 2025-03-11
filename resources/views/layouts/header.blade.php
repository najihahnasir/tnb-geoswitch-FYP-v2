<header id="header" class="header fixed-top d-flex align-items-center shadow-lg custom-header">

  <!-- Logo -->
  <div class="d-flex align-items-center justify-content-between">
    <a href="{{ route('dashboard') }}" class="logo d-flex align-items-center">
      <img src="{{ asset('assets/img/logo.png') }}" alt="UNI10 Logo" style="width: 40px; height: 40px; margin-right: 10px;">
      <span class="d-none d-lg-block text-uppercase fw-bold text-light">INFO-R(GRANT)</span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn text-light"></i>
  </div>
  <!-- End Logo -->

  <!-- Search Bar -->
  <div class="search-bar">
    <form class="search-form d-flex align-items-center" method="GET" action="{{ route('search') }}">
      <input type="text" name="query" class="form-control bg-light border-0 shadow-sm" placeholder="Search grants or users..." title="Enter search keyword">
      <button type="submit" title="Search" class="btn btn-primary ms-2 shadow"><i class="bi bi-search" style="color: white"></i></button>
    </form>
  </div>
  <!-- End Search Bar -->

  <!-- Navigation -->
  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">
      
      <!-- Profile -->
      <li class="nav-item dropdown pe-3">
        <a class="nav-link nav-profile d-flex align-items-center text-light" href="#" data-bs-toggle="dropdown">
          <i class="bi bi-person-circle" style="font-size: 2rem;"></i>
          <span class="d-none d-md-block dropdown-toggle ps-2">{{ auth()->user()->name }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile shadow">
          <li class="dropdown-header">
            <h6>{{ auth()->user()->name }}</h6>
            <span>{{ auth()->user()->userCategory }}</span>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <a class="dropdown-item d-flex align-items-center" href="{{ route('profile') }}">
              <i class="bi bi-person"></i>
              <span>My Profile</span>
            </a>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
              @csrf
              <button type="submit" class="dropdown-item d-flex align-items-center">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </button>
            </form>
          </li>
        </ul>
      </li>
      <!-- End Profile -->

    </ul>
  </nav>
  <!-- End Navigation -->

</header>
