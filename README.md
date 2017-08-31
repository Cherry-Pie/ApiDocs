# ApiDocs Generator
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Cherry-Pie/ApiDocs/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Cherry-Pie/ApiDocs/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/Cherry-Pie/ApiDocs/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Cherry-Pie/ApiDocs/build-status/master)


L5 API Documentation generator based upon DocBlock comments.


## Installation 

You can install the package through Composer:
```bash
composer require yaro/apidocs
```
Add this service provider and alias to ```config/app.php```:
```php
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

Then publish the config and assets files:
```bash
php artisan vendor:publish --provider="Yaro\ApiDocs\ServiceProvider"
```

And you should add a disk named snapshots to ```config/filesystems.php``` on which the [blueprint](https://apiblueprint.org) snapshots will be saved:
```php
//...
'disks' => [
    //...
    'apidocs' => [
        'driver' => 'local',
        'root'   => storage_path('apidocs'),
    ],
//...    
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

To exclude some routes/classes add them to config's ```exclude```. Asterisks may be used to indicate wildcards.
```php
'exclude' => [
    'classes' => [
        // 'App\Http\Controllers\*' - exclude all controllers from docs.
        // 'App\Http\Controllers\MyController@*' - remove all methods for specific controller from docs.
    ],
    
    'routes' => [
        // 'payment/test',
        // 'simulate/*',
    ],
 ],
```

Additionally you can create [API Blueprint](https://apiblueprint.org) file:
```php
ApiDocs::blueprint()->create();
// or pass snapshot name and/or filesystem disc name
ApiDocs::blueprint()->create('my-newest-snapshot', 's3-blueprint');
```
Or just render its contents without creating file:
```php
echo ApiDocs::blueprint()->render();
```
Or via ```artisan```:
```bash
php artisan apidocs:blueprint-create
```


## TODO
- generate plain html page with all documentation info.
- fullsize block with response.


## License
The MIT License (MIT). Please see [LICENSE](https://github.com/Cherry-Pie/ApiDocs/blob/master/LICENSE) for more information.
