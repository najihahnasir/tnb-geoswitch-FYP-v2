@extends('layouts.master') <!-- Extend the base layout -->

@section('title', 'GeoSwitch Dashboard') <!-- Set page title -->

@section('breadcrumbs')
<x-breadcrumbs 
    title="Welcome to GeoSwitch Dashboard"
    :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Home'],
        ['url' => '', 'label' => 'Main Dashboard']
    ]"
/>
@endsection

@section('content') 
<!-- Include Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" defer></script>

<script>
    function initMap() {
        const locations = [
            { lat: 3.139, lng: 101.6869, title: "Kuala Lumpur" },
            { lat: 3.0738, lng: 101.5183, title: "Shah Alam" },
            { lat: 2.922, lng: 101.650, title: "Kajang" }
        ];

        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 10,
            center: { lat: 3.139, lng: 101.6869 },
        });

        locations.forEach(location => {
            new google.maps.Marker({
                position: { lat: location.lat, lng: location.lng },
                map: map,
                title: location.title
            });
        });
    }
</script>

<style>
    #map { width: 100%; height: 400px; border-radius: 10px; }
    .dashboard-content { display: flex; gap: 20px; margin-top: 20px; }
    .chart-container { flex: 1; height: 250px; background: #f8f9fa; padding: 10px; border-radius: 10px; }
</style>

<h2>GeoSwitch Dashboard</h2>
<div id="map"></div>
<div class="dashboard-content">
    <div class="chart-container">Sensor Condition Analytics</div>
    <div class="chart-container">Maintenance Analytics</div>
    <div class="chart-container">Sensor Performance</div>
</div>
@endsection
