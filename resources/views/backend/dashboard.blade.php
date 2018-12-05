@extends('lap::layouts.auth')

@section('title', 'Dashboard')
@section('child-content')
    <h2>@yield('title')</h2>

    <div class="card">
        <div class="card-body">
            You are logged in!
        </div>
    </div>
@endsection