# Laravel API Builder

Scaffold CRUD API (Controller, Service, Repository, Resource, Requests, Policies, etc.) for Laravel applications.

## Features

- Generate complete CRUD API structure
- Create Controllers, Services, Repositories
- Generate Resources and Form Requests
- Create Policies for authorization
- Includes API error handling traits
- Custom exceptions for API responses

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

### Publish Stubs (Optional)

If you want to customize the generated files, publish the stub files:

```bash
php artisan vendor:publish --tag=api-builder-stubs
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

## Configuration

The package works out of the box with sensible defaults. All generated files follow Laravel conventions and best practices.

## Testing

```bash
composer test
```

## Credits

- [Mohamed ElSaka](https://github.com/melsaka)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.