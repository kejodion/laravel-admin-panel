<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css?family=Nunito:regular,bold" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('lap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('lap/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('lap/css/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('lap/css/easymde.min.css') }}">
    <link rel="stylesheet" href="{{ asset('lap/css/lap.css') }}">

    <title>@yield('title') | {{ config('app.name') }}</title>
</head>
<body class="@yield('body-class')"{!! session('flash') ? ' data-flash-class="'.session('flash.0').'" data-flash-message="'.session('flash.1').'"' : '' !!}>

@yield('parent-content')

<script src="{{ asset('lap/js/jquery.min.js') }}"></script>
<script src="{{ asset('lap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('lap/js/datatables.min.js') }}"></script>
<script src="{{ asset('lap/js/easymde.min.js') }}"></script>
<script src="{{ asset('lap/js/lap.js') }}"></script>
@stack('scripts')

</body>
</html>