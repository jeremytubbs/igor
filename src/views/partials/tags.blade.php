@foreach ($tags as $tag)
	<a href="{{ url('tags/'.$tag->slug) }}">{{ $tag->name }}</a>
@endforeach