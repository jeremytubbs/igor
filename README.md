## Igor Static Re-Animator

Igor is a simple yet powerful Laravel Package that transforms plain text into blog-aware dynamic content. Custom post content types can easily be built and new posts created using Artisan commands. All content types can be linked togther using Tags or Categories. Igor utilizes standard YAML frontmatter and Markdown within an opinionated folder structure to seed your content into its database schema. Igor will even process and save derivatives of images to your public directory.

**FYI - This project is pre-release and will have breaking changes.**

- [Installation](#installation)
	- [Composer](#composer)
	- [Service Provider](#service-provider)
	- [Publish Package Assets](#publish-package-assets)
- [Usage](#usage)
	- [Artisan Commands](#artisan-commands)
- [Example Folder Structure](#example-folder-structure)

## Installation

### Composer
Add the package via composer:
```sh
composer require jeremytubbs/igor
```

### Service Provider
Next, register the service provider in your `config/app.php` configuration file.
```php
Jeremytubbs\Igor\IgorServiceProvider::class,
```

### Publish Package Assets
```sh
php artisan vendor:publish
```

## Usage
### Artisan Commands

**Create a custom post type:**
```sh
php artisan igor:build project
```
This command will create a `resources\static\projects` directory. You will need to add `projects` to `app\config\igor.php` before you can publish any projects.

**Create a new post for a custom type:**
```sh
php artisan igor:new 'Hello World' --type=project
```
This command will create the file structure for a new project post.

**Watch the static folder for changes:**
```sh
php artisan igor:watch
```
> It's Alive!

## Example Folder Structure
```
resources/
└── static/
	├── config.yaml
	├── pages/
	|	└── about/
	|		├── images/
	|		└── index.md
	└── projects/
		├── config.yaml
		├── foo-bar/
		|	├── images/
		|	└── index.md
		└── hello-world/
			├── images/
			└── index.md
```


Todo Core:
- Asset workflow for non-images
- API
- more documentation!
