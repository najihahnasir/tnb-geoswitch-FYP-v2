@extends('layouts.niceadmin')

@section('title', 'Components / Cards') <!-- Optional: Title for the Page -->

@section('content')
<div class="pagetitle">
    <h1>Cards</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item">Components</li>
            <li class="breadcrumb-item active">Cards</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="row align-items-top">
        <div class="col-lg-6">
            <!-- Default Card -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Default Card</h5>
                    <p>Ut in ea error laudantium quas omnis officia. Sit sed praesentium voluptas...</p>
                </div>
            </div><!-- End Default Card -->

            <!-- Card with header and footer -->
            <div class="card">
                <div class="card-header">Header</div>
                <div class="card-body">
                    <h5 class="card-title">Card with header and footer</h5>
                    <p>Ut in ea error laudantium quas omnis officia. Sit sed praesentium voluptas...</p>
                </div>
                <div class="card-footer">
                    Footer
                </div>
            </div><!-- End Card with header and footer -->

            <!-- Card with an image on left -->
            <div class="card mb-3">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="{{ asset('assets/img/card.jpg') }}" class="img-fluid rounded-start" alt="...">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">Card with an image on left</h5>
                            <p>This is a wider card with supporting text below as a natural lead-in...</p>
                        </div>
                    </div>
                </div>
            </div><!-- End Card with an image on left -->
        </div>

        <div class="col-lg-3">
            <!-- Card with an image on top -->
            <div class="card">
                <img src="{{ asset('assets/img/card.jpg') }}" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Card with an image on top</h5>
                    <p>Some quick example text to build on the card title...</p>
                </div>
            </div><!-- End Card with an image on top -->

            <!-- Card with an image on bottom -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Card with an image on bottom</h5>
                    <p>Some quick example text to build on the card title...</p>
                </div>
                <img src="{{ asset('assets/img/card.jpg') }}" class="card-img-bottom" alt="...">
            </div><!-- End Card with an image on bottom -->
        </div>

        <div class="col-lg-3">
            <!-- Card with an image overlay -->
            <div class="card">
                <img src="{{ asset('assets/img/card.jpg') }}" class="card-img-top" alt="...">
                <div class="card-img-overlay">
                    <h5 class="card-title">Card with an image overlay</h5>
                    <p>Some quick example text to build on the card title...</p>
                </div>
            </div><!-- End Card with an image overlay -->
        </div>
    </div>
</section>
@endsection
