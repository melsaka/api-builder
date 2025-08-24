# Laravel API Builder

Scaffold CRUD API (Controller, Service, Repository, Resource, Requests, Policies, etc.) for Laravel applications.

## Features

- Generate complete CRUD API structure
- Create Controllers, Services, Repositories
- Generate Resources and Form Requests
- Create Policies for authorization
- Includes API error handling traits
- Custom exceptions for API responses
- Supports uploading images to your models

This package is using my [laravel image manager](https://github.com/melsaka/laravel-image-manager) package to support adding images easily to your models.

## Requirements

- PHP ^8.2
- Laravel ^11.0 or ^12.0

## Installation

You can install the package via Composer:

```bash
composer require melsaka/api-builder
```

The package will automatically register its service provider.

## Usage

### Publish Stubs

If you want to customize the generated files, publish the stub files:

```bash
php artisan vendor:publish --tag=api-builder-stubs
php artisan vendor:publish --tag=image-manager
```

### Generate Required Files

First, generate the base API files (controllers, traits, exceptions):

```bash
php artisan api:scaffold
```

### Generate CRUD for a Model

Generate complete CRUD for a specific model:

```bash
php artisan api:crud User
```

This will create:
- Controller: `app/Http/Controllers/Api/User/UserController.php`
- Service: `app/Services/UserService.php`
- Repository: `app/Repositories/UserRepository.php`
- Resource: `app/Http/Resources/UserResource.php`
- Requests: `app/Http/Requests/User/StoreUserRequest.php` & `UpdateUserRequest.php`
- Policy: `app/Policies/UserPolicy.php`
- Routes: `app/Routes/User.php`

### Uploading Images To Your Models

Checkout [laravel image manager](https://github.com/melsaka/laravel-image-manager) package to learn what you need to do first.

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Melsaka\ImageManager\ImageManagerServiceProvider" --tag="image-manager"
```

Then you create the images table: 

```bash
php artisan migrate
```

Add your model name and it's settings under the models attribute in the config file:

```php
return [
    'storage_disk' => 'public', // could be r2, aws, etc..
    'base_path' => 'uploads',
    'format' => 'webp',
    'quality' => 90,
    
    'models' => [
        'user' => [
            'types' => [
                'avatar' => [
                    'sizes' => [
                        'thumbnail' => [
                            'width' => 100,
                            'height' => 100,
                            'mode' => 'cover',
                        ],
                        'medium' => [
                            'width' => 300,
                            'height' => 300,
                            'mode' => 'cover',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
```

Add the Trait to your Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Melsaka\ImageManager\Traits\HasImages;

class User extends Model
{
    use HasImages;
    
    // Your model code...
}
```

Now all you need to do is to define the supported images function: 

```php
<?php
class User extends Model
{
    use HasImages;
	    
	public static function supportedImages()
	{
	    return [
	        'profile'   => 'singular', // for singular image upload
	        'gallery'   => 'multiple', // for multiple images upload
	    ];
	}
    // Your model code...
}
```

That's it now you can do:

```bash
php artisan api:crud User
```

For more info read about: [laravel image manager docs](https://github.com/melsaka/laravel-image-manager)

## Configuration

The package works out of the box with sensible defaults. All generated files follow Laravel conventions and best practices.

## Credits

- [Mohamed ElSaka](https://github.com/melsaka)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.