<?php 

namespace Yaro\ApiDocs\Exceptions;

use Exception;

class InvalidFilesystemDisc extends Exception 
{
    public function __construct($discName)
    {
        parent::__construct(sprintf('Cannot create filesystem disc [%s]', $discName));
    }
}
