<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">

    <!-- Common Links -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="bi bi-grid"></i><span>Dashboard</span>
        </a>
    </li>

    <!-- Admin Links -->
    @can('admin-only')
      <li class="nav-heading">Admin</li>
      <li class="nav-item">
          <a class="nav-link" href="{{ route('academicians.index') }}">
              <i class="bi bi-people"></i><span>Manage Academicians</span>
          </a>
      </li>
      <li class="nav-item">
          <a class="nav-link" href="{{ route('researchgrants.index') }}">
              <i class="bi bi-folder"></i><span>Manage Research Grants</span>
          </a>
      </li>
      <!-- Create New Grant -->
      <li class="nav-item">
        <a class="nav-link" href="{{ route('researchgrants.create') }}">
            <i class="bi bi-plus-circle"></i><span>Create New Grant</span>
        </a>
      </li>
    @endcan

    <!-- Leader Links -->
    @if(auth()->user()->isLeader())
    <li class="nav-heading">Leader</li>

    <!-- View Grants -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('researchgrants.index') }}">
            <i class="bi bi-folder"></i><span>View Grants</span>
        </a>
    </li>
    @endif

    <!-- iRMC Staff Links -->
    @if(auth()->user()->userCategory === 'iRMC Staff')
    <li class="nav-heading">iRMC Staff</li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('researchgrants.index') }}">
            <i class="bi bi-folder"></i><span>View Research Grants</span>
        </a>
    </li>
    @endif

    <!-- Member Links -->
    @if(auth()->user()->userCategory === 'Member' && !auth()->user()->isLeader())
    <li class="nav-heading">Member</li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('researchgrants.index') }}">
            <i class="bi bi-list-task"></i><span>View Participated Projects</span>
        </a>
    </li>
    @endif

    <!-- Common Actions -->
    <li class="nav-heading">Common</li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('profile') }}">
            <i class="bi bi-person"></i><span>Profile</span>
        </a>
    </li>
    {{-- <li class="nav-item">
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="nav-link btn btn-link p-0 text-start">
                <i class="bi bi-box-arrow-right"></i><span>Logout</span>
            </button>
        </form>
    </li> --}}
  </ul>
</aside>

