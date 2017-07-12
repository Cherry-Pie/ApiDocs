# ApiDocs Generator

L5 API Documentation generator based upon DocBlock comments.


## Installation 

You can install the package through Composer.
```bash
composer require yaro/apidocs
```
You must install this service provider. Make this the very first provider in list.
```php
// config/app.php
'providers' => [
    //...
    Yaro\ApiDocs\ServiceProvider::class,
    //...
]


'aliases' => [
    //...
    'ApiDocs' => Yaro\ApiDocs\Facade::class,
    //...
]
```

Then publish the config and assets files.
```bash
php artisan vendor:publish --provider="Yaro\ApiDocs\ServiceProvider"
```


## Usage
All your routes must begin with some segment, e.g. ```/api/``` (changed in config). 
Package will collect routes, that starts with this segment only.

Add to your route method DocBlock comment. 
e.g.:
```php
/**
 * Some api endpoint for important stuff.
 * 
 * Just show some template with     
 * some very long description    
 * on several lines
 * 
 * @param int    $offset   Just an offset size
 * @param string $password 
 */
public function getSomeStuff()
{
    return response()->json([]);
}
```

And create route to view your documentation.
```php
Route::get('/docs', function() {
    return ApiDocs::show();
});
```


## TODO
- add `global headers` section.
- generate plain html page with all documentation info.
- fullsize block with response.


## License
The MIT License (MIT). Please see [LICENSE](https://github.com/Cherry-Pie/ApiDocs/blob/master/LICENSE) for more information.
