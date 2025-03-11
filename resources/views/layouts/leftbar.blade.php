<!-- ============================================================== -->
<!-- Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="#" aria-expanded="false">
                        <i class="mdi mdi-comment-processing-outline"></i>
                        <span class="hide-menu">Dashboard </span>
                    </a>
                </li>

                <!-- Example of a dropdown menu -->
                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                        <i class="ri-draft-line"></i>
                        <span class="hide-menu"> View Report </span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item">
                            <a href="{{ route('academicians.index') }}" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu"> Download Report </span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu"> Export Report </span>
                            </a>
                        </li>
                        {{-- <li class="sidebar-item">
                            <a href="#" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu"> Pembatalan Pendaftaran </span>
                            </a>
                        </li> --}}
                    </ul>
                </li>

                <!-- Example of a single menu item -->
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="#" aria-expanded="false">
                        <i class="ri-folder-shared-line"></i>
                        <span class="hide-menu">Upload Dataset </span>
                    </a>
                </li>

                <!-- Example of another dropdown menu -->
                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                        <i class="ri-survey-line"></i>
                        <span class="hide-menu">GIS Substation </span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">Pre-F </span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">Post-F </span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- <!-- Example of a single menu item -->
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="#" aria-expanded="false">
                        <i class="ri-file-mark-line"></i>
                        <span class="hide-menu">Lesen </span>
                    </a>
                </li> --}}

                <!-- Example of a dropdown menu -->
                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                        <i class="ri-settings-2-line"></i>
                        <span class="hide-menu"> Setting </span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">#</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">#</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">#</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">#</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">#</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu-2">#</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Example of a single menu item -->
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="#" aria-expanded="false">
                        <i class="ri-book-2-line"></i>
                        <span class="hide-menu">Manage Account</span>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
<!-- ============================================================== -->
<!-- End Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->