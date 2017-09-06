<?php 

namespace Yaro\ApiDocs;

use Yaro\ApiDocs\Commands\BlueprintCreate;
use Yaro\ApiDocs\Http\Middleware\BasicAuth;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{

    protected $defer = false;

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/apidocs.php' => config_path('yaro.apidocs.php'),
        ], 'config');
        
        $this->app['view']->addNamespace('apidocs', __DIR__ . '/../resources/views');
        
        $this->app->bind('command.apidocs:blueprint-create', BlueprintCreate::class);
        $this->commands([
            'command.apidocs:blueprint-create',
        ]);
        
        $this->addMiddlewareAlias('apidocs.auth.basic', BasicAuth::class);
    } // end boot

    public function register()
    {
        $configPath = __DIR__ . '/../config/apidocs.php';
        $this->mergeConfigFrom($configPath, 'yaro.apidocs');
        
        $this->app->singleton('yaro.apidocs', function($app) {
            return $app->make(ApiDocs::class);
        });
    } // end register
    
    private function addMiddlewareAlias($name, $class)
    {
        $router = $this->app['router'];

        if (method_exists($router, 'aliasMiddleware')) {
            return $router->aliasMiddleware($name, $class);
        }

        return $router->middleware($name, $class);
    }
    
}
