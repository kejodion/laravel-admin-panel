@extends('lap::layouts.app')

@section('title', $doc->title . (!$doc->ancestors->isEmpty() ? ' | ' . $doc->ancestors->reverse()->implode('title', ' | ') : ''))
@section('body-class', 'bg-white')
@section('parent-content')
    <nav class="navbar navbar-expand navbar-dark bg-primary">
        <a class="sidebar-toggle mr-3" href="#"><i class="far fa-fw fa-bars"></i></a>
        <a class="navbar-brand" href="{{ route('docs') }}">{{ config('app.name') }}</a>
    </nav>

    <div class="wrapper d-flex">
        <div class="sidebar sidebar-dark bg-dark">
            <ul class="list-unstyled list-docs my-2">
                @foreach($docs as $d)
                    <li{!! $d->id == $doc->id ? ' class="active"' : '' !!}>
                        @if($d->type == 'Menu Heading')
                            <b{!! !$d->depth ? ' class="mt-2"' : '' !!}>
                                <span style="margin-left: {{ $d->depth * 1 }}rem">{{ $d->title }}</span>
                            </b>
                        @else
                            <a href="{{ $d->type == 'Index' ? route('docs') : route('docs', ['id' => $d->id, 'slug' => $d->slug]) }}">
                                <span style="margin-left: {{ $d->depth * 1 }}rem">{{ $d->title }}</span>
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="content markdown p-3 p-md-5">
            {!! $doc->markdown() !!}
        </div>
    </div>
@endsection