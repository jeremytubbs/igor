@extends('app')

@section('content')
	@foreach ($contents as $content)
		{{ $content }}
	@endforeach
@endsection