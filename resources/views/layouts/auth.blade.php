@extends('lap::layouts.app')

@section('body-class', 'bg-grey')
@section('parent-content')
    <nav class="navbar navbar-expand navbar-dark bg-primary">
        <a class="sidebar-toggle mr-3" href="#"><i class="far fa-fw fa-bars"></i></a>
        <a class="navbar-brand" href="{{ route('admin') }}">{{ config('app.name') }}</a>

        <div class="navbar-collapse collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="{{ route('docs') }}" class="nav-link" target="_blank">
                        <i class="fal fa-question-circle"></i> <span class="d-none d-md-inline">Docs</span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" id="userDropdown" class="nav-link dropdown-toggle" data-toggle="dropdown">
                        <i class="fal fa-user-circle"></i> <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <a href="{{ route('admin.profile') }}" class="dropdown-item{{ request()->is('admin/profile') ? ' active' : '' }}">Update Profile</a>
                        <a href="{{ route('admin.password.change') }}" class="dropdown-item{{ request()->is('admin/password/change') ? ' active' : '' }}">Change Password</a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <a href="#" id="logout_link" class="dropdown-item">Logout</a>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div class="wrapper d-flex">
        <div class="sidebar sidebar-dark bg-dark">
            <ul class="list-unstyled list-admin mb-0">
                @include('lap::layouts.menu')
            </ul>
        </div>

        <div class="content p-3 p-md-5">
            @yield('child-content')
        </div>
    </div>
@endsection