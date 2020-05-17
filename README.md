## LifeOnScreen/nova-sort-relations

This package improves support for sorting relations in Laravel Nova.

## Installation

Install via composer

``` bash
$ composer require lifeonscreen/nova-sort-relations
```

## Usage

Include `LifeOnScreen\SortRelations\SortRelations` trait to your class. Define base by overriding `indexQuery`.
Define sortable columns in `$sortRelations` array.

```php

...
use LifeOnScreen\SortRelations\SortRelations;
...

class Product extends Resource
{
    public static $sortRelations = [
        // Order product relation by product id...
        'product'               => 'id',
        // overriding user relation sorting
        'user'         => [
            // sorting multiple columns
            'name',
            'surname',
        ],
        // overriding company relation sorting
        'company'          => 'name',
    ];
    
    public static function indexQuery(NovaRequest $request, $query)
    {
        // You can modify your base query here, only if necessary. Sort Relations will be applied automatically...
        return $query;
    }
}

```


## Security

If you discover any security-related issues, please email the author instead of using the issue tracker.

## Credits 
- [Jani Cerar](https://github.com/janicerar)

## License

MIT license. Please see the [license file](docs/license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/lifeonscreen/nova-sort-relations.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/lifeonscreen/nova-sort-relations.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/lifeonscreen/nova-sort-relations
[link-downloads]: https://packagist.org/packages/lifeonscreen/nova-sort-relations
[link-author]: https://github.com/LifeOnScreen
