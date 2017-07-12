<?php 

namespace Yaro\ApiDocs;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    protected $defer = false;

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/apidocs.php' => config_path('yaro.apidocs.php'),
        ], 'config');
        
        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/yaro/apidocs'),
        ], 'public');
        
        $this->app['view']->addNamespace('apidocs', __DIR__ . '/../resources/views');
    } // end boot

    public function register()
    {
        $configPath = __DIR__ . '/../config/apidocs.php';
        $this->mergeConfigFrom($configPath, 'yaro.apidocs');
        
        $this->app->singleton('yaro.apidocs', function($app) {
            return $app->make(ApiDocs::class);
        });
    } // end register
    
}
