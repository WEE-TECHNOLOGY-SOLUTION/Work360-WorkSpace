@extends('sign-in-with-google::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>
        This view is loaded from module: {!! config('sign-in-with-google.name') !!}
    </p>
@endsection
