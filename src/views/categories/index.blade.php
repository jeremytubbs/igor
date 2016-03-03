@extends('app')

@section('content')
	@foreach ($categories as $category)
		<a href="{{ url('categories/'.$category->name) }}">{{ $category->name }}</a>
	@endforeach
@endsection