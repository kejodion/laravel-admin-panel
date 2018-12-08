@extends('lap::layouts.auth')

@section('title', 'Update Doc')
@section('child-content')
    <h2>@yield('title')</h2>

    <form method="POST" action="{{ route('admin.docs.update', $doc->id) }}" novalidate data-ajax-form>
        @csrf
        @method('PATCH')

        <div class="list-group">
            @if(!$doc->system)
                <div class="list-group-item">
                    <div class="form-group row mb-0">
                        <label for="type" class="col-md-2 col-form-label">Type</label>
                        <div class="col-md-8">
                            <select name="type" id="type" class="form-control" data-show-hide=".type-option">
                                @foreach(['Menu Heading', 'Page'] as $type)
                                    <option value="{{ $type }}" data-show="{{ $type }}"{{ $doc->type == $type ? ' selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="list-group-item">
                    <div class="form-group row mb-0">
                        <label for="parent_id" class="col-md-2 col-form-label">Parent</label>
                        <div class="col-md-8">
                            <select name="parent_id" id="parent_id" class="form-control">
                                <option value=""></option>
                                @foreach($docs as $d)
                                    <option value="{{ $d->id }}"{{ $doc->parent_id == $d->id ? ' selected' : '' }}>{!! str_repeat('&nbsp;', $d->depth * 3) !!}{{ $d->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            <div class="list-group-item">
                <div class="form-group row mb-0">
                    <label for="title" class="col-md-2 col-form-label">Title</label>
                    <div class="col-md-8">
                        <input type="text" name="title" id="title" class="form-control" value="{{ $doc->title }}">
                    </div>
                </div>
            </div>

            <div class="list-group-item type-option{{ $doc->type == 'Menu Heading' ? ' d-none' : '' }}" data-show="Page">
                <div class="form-group row mb-0">
                    <label for="content" class="col-md-2 col-form-label">Content</label>
                    <div class="col-md-8">
                        <textarea name="content" id="content" class="form-control form-control-markdown">{{ $doc->content }}</textarea>
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