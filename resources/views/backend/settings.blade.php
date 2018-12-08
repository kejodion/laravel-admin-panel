@extends('lap::layouts.auth')

@section('title', 'Settings')
@section('child-content')
    <h2>@yield('title')</h2>

    <form method="POST" action="{{ route('admin.settings') }}" novalidate data-ajax-form>
        @csrf
        @method('PATCH')

        <div class="list-group">
            <div class="list-group-item">
                <div class="form-group row mb-0">
                    <label for="example" class="col-md-2 col-form-label">Example</label>
                    <div class="col-md-8">
                        <input type="text" name="example" id="example" class="form-control" value="{{ config('settings.example') }}">
                    </div>
                </div>
            </div>

            <div class="list-group-item bg-light text-left text-md-right">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </form>
@endsection