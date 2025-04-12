@extends('layouts.master')

@section('title', 'Upload Dataset')

@section('content')
<h2>Upload Switchgear Dataset</h2>

@if(session('success'))
    <div style="color: green">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div style="color: red">{{ session('error') }}</div>
@endif

<form action="{{ route('datasets.upload') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label>Select CSV File:</label>
    <input type="file" name="csv_file" required>
    <button type="submit">Upload</button>
</form>
@endsection
