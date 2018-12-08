@extends('lap::layouts.auth')

@section('title', 'Update Role')
@section('child-content')
    <h2>@yield('title')</h2>

    <form method="POST" action="{{ route('admin.roles.update', $role->id) }}" novalidate data-ajax-form>
        @csrf
        @method('PATCH')

        <div class="list-group">
            <div class="list-group-item">
                <div class="form-group row mb-0">
                    <label for="name" class="col-md-2 col-form-label">Name</label>
                    <div class="col-md-8">
                        <input type="text" name="name" id="name" class="form-control" value="{{ $role->name }}">
                    </div>
                </div>
            </div>

            <div class="list-group-item">
                <div class="form-group row mb-0">
                    <label class="col-md-2 col-form-label">Permissions</label>
                    <div class="col-md-8">
                        <div class="form-control-plaintext">
                            @if($role->admin)
                                This role always has all permissions.
                            @else
                                @foreach ($group_permissions as $group => $permissions)
                                    <b class="d-block{{ !$loop->first ? ' mt-3' : '' }}">{{ $group }}</b>
                                    @foreach ($permissions as $permission)
                                        <div class="custom-control custom-control-inline custom-checkbox">
                                            <input type="checkbox" name="permissions[]" id="permission_{{ $permission->id }}" class="custom-control-input" value="{{ $permission->id }}"
                                                    {{ $role->permissions->contains('id', $permission->id) ? ' checked' : '' }}>
                                            <label for="permission_{{ $permission->id }}" class="custom-control-label">{{ $permission->name }}</label>
                                        </div>
                                    @endforeach
                                @endforeach
                            @endif
                        </div>
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