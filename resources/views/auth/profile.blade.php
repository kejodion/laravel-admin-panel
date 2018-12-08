@extends('lap::layouts.auth')

@section('title', 'Update Profile')
@section('child-content')
    <h2>@yield('title')</h2>

    <form method="POST" action="{{ route('admin.profile') }}" novalidate data-ajax-form>
        @csrf
        @method('PATCH')

        <div class="list-group">
            <div class="list-group-item">
                <div class="form-group row mb-0">
                    <label for="name" class="col-md-2 col-form-label">Name</label>
                    <div class="col-md-8">
                        <input type="text" name="name" id="name" class="form-control" value="{{ auth()->user()->name }}">
                    </div>
                </div>
            </div>

            <div class="list-group-item">
                <div class="form-group row mb-0">
                    <label for="email" class="col-md-2 col-form-label">Email</label>
                    <div class="col-md-8">
                        <input type="email" name="email" id="email" class="form-control" value="{{ auth()->user()->email }}">
                    </div>
                </div>
            </div>

            <div class="list-group-item bg-light text-left text-md-right">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </form>
@endsection