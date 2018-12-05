@extends('lap::layouts.auth')

@section('title', 'Activity Logs')
@section('child-content')
    <h2>@yield('title')</h2>

    <div class="card">
        <div class="card-body">
            {!! $html->table() !!}
        </div>
    </div>
@endsection

@push('scripts')
    {!! $html->scripts() !!}
@endpush