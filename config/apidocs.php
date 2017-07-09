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
     * Image path for logo
     */
    'logo' => '/vendor/yaro/apidocs/images/logo.png',

];
