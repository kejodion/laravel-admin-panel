@extends('lap::layouts.auth')

@section('title', 'Role')
@section('child-content')
    <div class="row mb-3">
        <div class="col-md">
            <h2 class="mb-0">@yield('title')</h2>
        </div>
        <div class="col-md-auto mt-2 mt-md-0">
            @can('Update Roles')
                <a href="{{ route('admin.roles.update', $role->id) }}" class="btn btn-primary">Update</a>
            @endcan
            @can('Delete Roles')
                <form method="POST" action="{{ route('admin.roles.delete', $role->id) }}" class="d-inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-primary" data-confirm{{ $role->admin ? ' disabled' : '' }}>Delete</button>
                </form>
            @endcan
        </div>
    </div>

    <div class="list-group">
        <div class="list-group-item">
            <div class="row">
                <div class="col-md-2">ID</div>
                <div class="col-md-8">{{ $role->id }}</div>
            </div>
        </div>

        <div class="list-group-item">
            <div class="row">
                <div class="col-md-2">Name</div>
                <div class="col-md-8">{{ $role->name }}</div>
            </div>
        </div>

        <div class="list-group-item">
            <div class="row">
                <div class="col-md-2">Permissions</div>
                <div class="col-md-8">
                    @if($role->admin)
                        This role always has all permissions.
                    @else
                        {{ $role->permissions->sortBy('id')->implode('name', ', ') }}
                    @endif
                </div>
            </div>
        </div>

        <div class="list-group-item">
            <div class="row">
                <div class="col-md-2">Created At</div>
                <div class="col-md-8">{{ $role->created_at }}</div>
            </div>
        </div>

        <div class="list-group-item">
            <div class="row">
                <div class="col-md-2">Updated At</div>
                <div class="col-md-8">{{ $role->updated_at }}</div>
            </div>
        </div>
    </div>
@endsection