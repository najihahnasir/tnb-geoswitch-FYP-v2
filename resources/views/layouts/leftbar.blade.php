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
                    <a class="sidebar-link waves-effect waves-dark" href="{{ route('dashboard') }}" aria-expanded="false">
                        <i class="mdi mdi-comment-processing-outline"></i>
                        <span class="hide-menu">Dashboard </span>
                    </a>
                </li>

                <!-- Example of a dropdown menu -->
                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                        <i class="ri-draft-line"></i>
                        <span class="hide-menu">Substation</span>
                    </a>

                    {{-- <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item">
                            <a href="{{ route('reports.index') }}" class="sidebar-link">
                                <i class="mdi mdi-eye"></i>
                                <span class="hide-menu"> Sensor Status Summary </span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link" target="_blank">
                                <i class="mdi mdi-hospital-building"></i>
                                <span class="hide-menu"> Substation Health Status</span>
                            </a>
                        </li>
                        
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link" target="_blank">
                                <i class="mdi mdi-comment-processing-outline"></i>
                                <i class="mdi mdi-bell-alert"></i>
                                <span class="hide-menu"> Critical Alarms List </span>
                            </a>
                        </li>
                    </ul> --}}
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item">
                            <a href="{{ route('substations.summary') }}" class="sidebar-link">
                                <i class="mdi mdi-eye"></i>
                                <span class="hide-menu"> Substation Summary </span>
                            </a>
                        </li>
                        {{-- <li class="sidebar-item">
                            <a href="{{ route('reports.index') }}" class="sidebar-link">
                                <i class="mdi mdi-eye"></i>
                                <span class="hide-menu"> Substation Report </span>
                            </a>
                        </li> --}}
                        <li class="sidebar-item">
                            <a href="{{ route('substations.create') }}" class="sidebar-link">
                                <i class="mdi mdi-hospital-building"></i>
                                <span class="hide-menu"> Register New Substation</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Example of a single menu item -->
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark has-arrow" href="#" aria-expanded="false" data-bs-toggle="collapse" data-bs-target="#sensorSummaryMenu">
                        <i class="ri-folder-shared-line"></i>
                        <span class="hide-menu">Sensor</span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level" id="sensorSummaryMenu">
                        <li class="sidebar-item">
                            <a href="{{ route('sensors.index') }}" class="sidebar-link">
                                <i class="ri-file-upload-line"></i>
                                <span class="hide-menu">Sensors Status Summary</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('sensors.create') }}" class="sidebar-link">
                                <i class="ri-eye-line"></i>
                                <span class="hide-menu">Register New Sensor</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('log-reports.index') }}" class="sidebar-link">
                                <i class="ri-file-upload-line"></i>
                                <span class="hide-menu">Report Offline Sensor</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('log-reports.create') }}" class="sidebar-link">
                                <i class="ri-file-upload-line"></i>
                                <span class="hide-menu">Create Report Offline </span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Example of another dropdown menu -->
                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                        <i class="ri-survey-line"></i>
                        <span class="hide-menu">Analytics</span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item">
                            <a href="{{ route('analytics.substations') }}" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">Substation Analytics </span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('analytics.sensors') }}" class="sidebar-link">
                                <i class="mdi mdi-adjust"></i>
                                <span class="hide-menu">Sensor Analytics</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Example of a single menu item -->
                {{-- <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="{{ route('datasets.upload.form') }}" aria-expanded="false">
                        <i class="ri-file-mark-line"></i>
                        <span class="hide-menu">Upload </span>
                    </a>
                </li> --}}

                <!-- Example of a dropdown menu -->
                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                        <i class="ri-settings-2-line"></i>
                        <span class="hide-menu"> Setting </span>
                    </a>
                    {{-- <ul aria-expanded="false" class="collapse first-level">
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
                    </ul> --}}
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