<?php

return [

    /**
     * Prefix of routes, that will be used in generated documentation.
     */
    'prefix' => 'api',
    
    /**
     * Remove namespaces from sidebar menu.
     * 
     * Ex: if namespace is My\Name\Space\Foo and `My` and `Space` are disabled, 
     * then sidebar menu will be:
     * Name
     *  - Foo
     */
    'disabled_namespaces' => [
        //'App', 
        //'Http',
        //'Controllers',
    ],
    
    /**
     * Options for basic auth middleware.
     */
    'auth' => [
        'enabled' => false,
    
        'credentials' => [
            // ['username', 'password'],
        ],
    ],
    
    /**
     * Exclude specific routes from documentation. Asterisks may be used to indicate wildcards.
     */
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
    
    /**
     * Image src for logo.
     */
    'logo' => '',
    
    /**
     * API Blueprint related data.
     */
    'blueprint' => [
 
        'host' => null,
        
        'title' => 'API Documentation',
        'introduction' => '',
        
        'reference_delimiter' => ' / ',
        
        /**
         * Filesystem's disc for storing blueprint snapshots.
         */
        'disc' => 'apidocs',
    
    ],

];
