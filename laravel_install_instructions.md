# Installing Payment Package in Laravel 12

## Method 1: Install from Repository

To install this package in Laravel 12, you need to specify the development version explicitly in your Laravel project's composer.json:

```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^12.0",
        "graigdev/payment": "dev-develop"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/yourusername/payment"
        }
    ]
}
```

Then run:
```bash
composer update
```

## Method 2: Install from Local Path

If you're developing locally, you can use path repository:

```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^12.0",
        "graigdev/payment": "@dev"
    },
    "repositories": [
        {
            "type": "path",
            "url": "../payment",
            "options": {
                "symlink": true
            }
        }
    ]
}
```

## Method 3: Create a Release Tag

For a more permanent solution, create a release tag in your repository:

```bash
git tag -a v1.0.0 -m "First stable release"
git push origin v1.0.0
```

Then users can install with:
```bash
composer require graigdev/payment:^1.0
``` 