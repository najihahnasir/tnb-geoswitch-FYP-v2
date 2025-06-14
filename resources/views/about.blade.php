@extends('layouts.master')

@section('title', 'About This Project')
@section('page-title', 'About TNB-GEOSWITCH')

@section('content')
<div class="container my-2">
    <div class="card shadow-sm p-4">
        <h4 class="mb-3">📌 Final Year Project Overview</h4>

        <p>
            This Final Year Project (FYP) is developed in collaboration with the <strong>Asset Management Department</strong>, under the <strong>Distribution Network Division</strong> of <strong>Tenaga Nasional Berhad (TNB)</strong>. The project represents a meaningful academic-industry initiative aiming to support smart grid transformation and improve asset performance management.
        </p>

        <p>
            The system — <strong>TNB-GEOSWITCH: Localization Platform for Switchgear Sensor Performance Monitoring in TNB Substation Using GIS</strong> — is a web-based platform designed to monitor, localize, and visualize switchgear sensor performance data across substations. By integrating <strong>Geographic Information System (GIS)</strong) capabilities, the platform provides real-time geospatial insights to assist TNB engineers in proactive decision-making and fault detection.
        </p>

        <p>
            Key features of the system include interactive GIS-based dashboards, real-time sensor status monitoring, historical analytics, dynamic data filtering, and automated downtime tracking — all contributing to enhanced operational reliability across the power distribution network.
        </p>

        <hr>

        <h5 class="mt-4">👨‍💻 Developer Profile</h5>
        <ul class="mb-3">
            <li><strong>Full Name:</strong> Nurul Najihah Binti Mohd Nasir</li>
            <li><strong>Institution:</strong> Universiti Tenaga Nasional (UNITEN)</li>
            <li><strong>Programme:</strong> Bachelor of Software Engineering (Hons)</li>
            <li><strong>Expected Graduation:</strong> 2025</li>
            <li><strong>Email:</strong> sw01082302@student.uniten.edu.my</li>
        </ul>

        <p class="mb-0">
            This project was independently developed with technical guidance from TNB engineers and academic supervision from UNITEN lecturers.
        </p>

        <hr>

        <h5 class="mt-4">🛠️ Tools & Technologies</h5>
        <ul>
            <li><strong>Backend:</strong> Laravel (PHP Framework)</li>
            <li><strong>Frontend:</strong> Blade Templating Engine, Bootstrap 5</li>
            <li><strong>GIS Mapping:</strong> Leaflet.js</li>
            <li><strong>Data Visualization:</strong> Chart.js (with custom plugins)</li>
            <li><strong>Database:</strong> MySQL (Structured Relational Design)</li>
        </ul>

        <hr>

        <p class="text-muted small mt-4">
            <strong>Disclaimer:</strong> All data presented within this platform are simulated and used solely for academic demonstration. This system is not connected to live operational infrastructure. Feedback and validation were provided by technical representatives from TNB’s Distribution Network Division throughout the development process.
        </p>
    </div>
</div>
@endsection
