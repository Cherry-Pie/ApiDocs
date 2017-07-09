<?php 

namespace Yaro\ApiDocs;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

class Facade extends IlluminateFacade 
{
 
    protected static function getFacadeAccessor() 
    {
        return 'yaro.apidocs'; 
    } // end getFacadeAccessor
 
}
