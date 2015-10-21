## Igor Static ReAnimator Package

For use with Laravel 5.1

Add service provider to app/config -

`
Jeremytubbs\Igor\IgorServiceProvider::class,
`

Then run -
`
php artisan vendor:publish
`

Better documentation coming soon!

Todo -

- add type to config igor.php on build
- set character limit for meta description
- auto fill meta description from content
- set post excerpt
- use publish date in folder names?
- add custom fields via build command
- use custom fields to build migration
- register custom events in static config
- transformer for api response
- read api of static type posts
- write api for static type
