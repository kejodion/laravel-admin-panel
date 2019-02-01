@extends('lap::layouts.auth')

@section('title', 'Doc')
@section('child-content')
    <div class="row mb-3">
        <div class="col-md">
            <h2 class="mb-0">@yield('title')</h2>
        </div>
        <div class="col-md-auto mt-2 mt-md-0">
            @can('Update Docs')
                <a href="{{ route('admin.docs.update', $doc->id) }}" class="btn btn-primary">Update</a>
            @endcan
            @can('Delete Docs')
                <form method="POST" action="{{ route('admin.docs.delete', $doc->id) }}" class="d-inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-primary" data-confirm{{ $doc->system ? ' disabled' : '' }}>Delete</button>
                </form>
            @endcan
        </div>
    </div>

    <div class="list-group">
        <div class="list-group-item">
            <div class="row">
                <div class="col-md-2">ID</div>
                <div class="col-md-8">{{ $doc->id }}</div>
            </div>
        </div>

        <div class="list-group-item">
            <div class="row">
                <div class="col-md-2">Type</div>
                <div class="col-md-8">{{ $doc->type }}</div>
            </div>
        </div>

        @if(!$doc->system)
            <div class="list-group-item">
                <div class="row">
                    <div class="col-md-2">Parent</div>
                    <div class="col-md-8">{{ $doc->ancestors->implode('title', ' -> ') }}</div>
                </div>
            </div>
        @endif

        <div class="list-group-item">
            <div class="row">
                <div class="col-md-2">Title</div>
                <div class="col-md-8">{{ $doc->title }}</div>
            </div>
        </div>

        @if($doc->type == 'Page')
            <div class="list-group-item">
                <div class="row">
                    <div class="col-md-2">Slug</div>
                    <div class="col-md-8">{{ $doc->slug }}</div>
                </div>
            </div>
        @endif

        @if($doc->type != 'Menu Heading')
            <div class="list-group-item">
                <div class="row">
                    <div class="col-md-2">Content</div>
                    <div class="col-md-8 markdown">{!! $doc->markdown() !!}</div>
                </div>
            </div>
        @endif

        <div class="list-group-item">
            <div class="row">
                <div class="col-md-2">Created At</div>
                <div class="col-md-8">{{ $doc->created_at }}</div>
            </div>
        </div>

        <div class="list-group-item">
            <div class="row">
                <div class="col-md-2">Updated At</div>
                <div class="col-md-8">{{ $doc->updated_at }}</div>
            </div>
        </div>
    </div>
@endsection