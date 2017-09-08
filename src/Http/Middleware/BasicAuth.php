<?php

namespace Yaro\ApiDocs\Http\Middleware;

class BasicAuth
{
    
    public function handle($request, $next)
    {
        if (!$this->isAuthorized($request)) {
            return response('Unauthorized', 401, [
                'WWW-Authenticate' => 'Basic',
            ]);
        }
        
        return $next($request);
    }
    
    private function isAuthorized($request) 
    {
        if (!config('yaro.apidocs.auth.enabled', false)) {
            return true;
        }
        
        $authorized = collect(config('yaro.apidocs.auth.credentials', []));
        $credentials = [
            $request->getUser(), 
            $request->getPassword()
        ];
        
        return !$authorized->contains($credentials);
    }
    
}
