@extends('lap::layouts.guest')

@section('title', 'Reset Password')
@section('child-content')
    <form method="POST" action="{{ route('admin.password.email') }}" novalidate data-ajax-form>
        @csrf

        <div class="form-group">
            <input type="email" name="email" id="email" class="form-control" placeholder="Email Address">
        </div>

        <button type="submit" class="btn btn-block btn-primary">Send Password Reset Link</button>
    </form>
@endsection