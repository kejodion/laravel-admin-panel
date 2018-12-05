@extends('lap::layouts.guest')

@section('title', 'Reset Password')
@section('child-content')
    <form method="POST" action="{{ route('admin.password.update') }}" novalidate data-ajax-form>
        @csrf

        <div class="form-group">
            <input type="email" name="email" id="email" class="form-control" placeholder="Email Address">
        </div>

        <div class="form-group">
            <input type="password" name="password" id="password" class="form-control" placeholder="New Password">
        </div>

        <div class="form-group">
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm New Password">
        </div>

        <input type="hidden" name="token" value="{{ $token }}">

        <button type="submit" class="btn btn-block btn-primary">Reset Password</button>
    </form>
@endsection