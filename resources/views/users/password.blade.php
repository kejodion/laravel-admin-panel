@extends('lap::layouts.auth')

@section('title', 'Change User Password')
@section('child-content')
    <h2>@yield('title')</h2>

    <form method="POST" action="{{ route('admin.users.password', $user->id) }}" novalidate data-ajax-form>
        @csrf
        @method('PATCH')

        <div class="list-group">
            <div class="list-group-item">
                <div class="form-group row mb-0">
                    <label for="new_password" class="col-md-2 col-form-label">New Password</label>
                    <div class="col-md-8">
                        <input type="password" name="new_password" id="new_password" class="form-control">
                    </div>
                </div>
            </div>

            <div class="list-group-item">
                <div class="form-group row mb-0">
                    <label for="new_password_confirmation" class="col-md-2 col-form-label">Confirm New Password</label>
                    <div class="col-md-8">
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control">
                    </div>
                </div>
            </div>

            <div class="list-group-item bg-light text-left text-md-right pb-1">
                <button type="submit" name="_submit" class="btn btn-primary mb-2" value="reload_page">Save</button>
                <button type="submit" name="_submit" class="btn btn-primary mb-2" value="redirect">Save &amp; Go Back</button>
            </div>
        </div>
    </form>
@endsection