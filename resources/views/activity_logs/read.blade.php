@extends('lap::layouts.auth')

@section('title', 'Activity Log')
@section('child-content')
    <h2>@yield('title')</h2>

    <div class="list-group">
        <div class="list-group-item">
            <div class="row">
                <div class="col-md-2">ID</div>
                <div class="col-md-8">{{ $activity_log->id }}</div>
            </div>
        </div>

        <div class="list-group-item">
            <div class="row">
                <div class="col-md-2">Created At</div>
                <div class="col-md-8">{{ $activity_log->created_at }}</div>
            </div>
        </div>

        <div class="list-group-item">
            <div class="row">
                <div class="col-md-2">Message</div>
                <div class="col-md-8">{{ $activity_log->message }}</div>
            </div>
        </div>

        <div class="list-group-item">
            <div class="row">
                <div class="col-md-2">User</div>
                <div class="col-md-8">{{ $activity_log->user->name }}</div>
            </div>
        </div>

        @if($activity_log->data)
            <div class="list-group-item">
                <div class="row">
                    <div class="col-md-2">Data</div>
                    <div class="col-md-8">
                        <pre class="mb-0">{{ stripcslashes(json_encode($activity_log->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) }}</pre>
                    </div>
                </div>
            </div>
        @endif

        @if($model = $activity_log->model())
            <div class="list-group-item">
                <div class="row">
                    <div class="col-md-2">Model</div>
                    <div class="col-md-8">
                        <pre class="mb-0">{{ stripcslashes(json_encode($model->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) }}</pre>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection