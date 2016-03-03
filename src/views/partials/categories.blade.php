@foreach ($categories as $category)
	<a href="{{ url('categories/'.$category->slug) }}">{{ $category->name }}</a>
@endforeach