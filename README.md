## Igor Static ReAnimator Package

For use with Laravel 5.1

Add service provider to app/config -
`
Jeremytubbs\Igor\IgorServiceProvider::class,
`

php artisan vendor:publish

create resources/static/posts folder
create resources/static/config.yaml

define

Todo -

- create static folder structure on vendor:publish
- config for default posts route name
- use publish date in folder names?
- add custom fields via build command
- use custom fields to build migration
- register custom events in static config
- transformer for api response
- read api of static type posts
- write api for static type
