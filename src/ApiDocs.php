<?php 

namespace Yaro\ApiDocs;

use ReflectionClass;
use Illuminate\Routing\Router;
use Illuminate\Contracts\Config\Repository as Config;
use Yaro\ApiDocs\Blueprint;

class ApiDocs
{
    
    private $router;
    private $config;
    
    public function __construct(Router $router, Config $config)
    {
        $this->router = $router;
        $this->config = $config;
    } // end __construct

    public function show()
    {
        $endpoints = $this->getEndpoints();
        $endpoints = $this->getSortedEndpoints($endpoints);
        
        return view('apidocs::docs', compact('endpoints'));
    } // end show

    public function blueprint()
    {
        $blueprint = app()->make(Blueprint::class);
        $blueprint->setEndpoints($this->getEndpoints());
        
        return $blueprint;
    } // end blueprint
    
    private function getEndpoints()
    {
        $endpoints = [];
        
        foreach ($this->router->getRoutes() as $route) {
            if (!$this->isPrefixedRoute($route) || $this->isClosureRoute($route) || $this->isExcluded($route)) {
                continue;
            }
            
            $actionController = explode("@", $this->getRouteParam($route, 'action.controller'));
            $class  = $actionController[0];
            $method = $actionController[1];
            
            if (!class_exists($class) || !method_exists($class, $method)) {
                continue;
            }
            
            list($title, $description, $params) = $this->getRouteDocBlock($class, $method);
            $key = $this->generateEndpointKey($class);
            
            $endpoints[$key][] = [
                'hash'    => $this->generateHashForUrl($key, $route, $method),
                'uri'     => $this->getRouteParam($route, 'uri'),
                'name'    => $method,
                'methods' => $this->getRouteParam($route, 'methods'),
                'docs' => [
                    'title'       => $title, 
                    'description' => trim($description), 
                    'params'      => $params,
                    'uri_params'  => $this->getUriParams($route),
                ],
            ];
        }
        
        return $endpoints;
    } // end getEndpoints
    
    private function isExcluded($route)
    {
        $uri = $this->getRouteParam($route, 'uri');
        $actionController = $this->getRouteParam($route, 'action.controller');
        
        return $this->isExcludedClass($actionController) || $this->isExcludedRoute($uri);
    } // end isExcluded
    
    private function isExcludedRoute($uri)
    {
        foreach ($this->config->get('yaro.apidocs.exclude.routes', []) as $pattern) {
            if (str_is($pattern, $uri)) {
                return true;
            }
        }
        
        return false;
    } // end isExcludedRoute
    
    private function isExcludedClass($actionController)
    {
        foreach ($this->config->get('yaro.apidocs.exclude.classes', []) as $pattern) {
            if (str_is($pattern, $actionController)) {
                return true;
            }
        }
        
        return false;
    } // end isExcludedClass
    
    private function isPrefixedRoute($route)
    {
        $prefix = $this->config->get('yaro.apidocs.prefix', 'api');
        $regexp = '~^'. preg_quote($prefix) .'~';
        
        return preg_match($regexp, $this->getRouteParam($route, 'uri'));
    } // end isPrefixedRoute
    
    private function isClosureRoute($route)
    {
        $action = $this->getRouteParam($route, 'action.uses');
        
        return is_object($action);
    } // end isClosureRoute
    
    private function getRouteDocBlock($class, $method)
    {
        $reflector = new ReflectionClass($class);
        
        $title = implode(' ', $this->splitCamelCaseToWords($method));
        $title = ucfirst(strtolower($title));
        $description = '';
        $params = [];
            
        $docs = explode("\n", $reflector->getMethod($method)->getDocComment());
        $docs = array_filter($docs);
        if (!$docs) {
            return [$title, $description, $params];
        }
        
        $docs = $this->filterDocBlock($docs);
        
        $title = array_shift($docs);
        
        $checkForLongDescription = true;
        foreach ($docs as $line) {
            if ($checkForLongDescription && !preg_match('~^@\w+~', $line)) {
                $description .= trim($line) .' ';
            } elseif (preg_match('~^@\w+~', $line)) {
                $checkForLongDescription = false;
                if (preg_match('~^@param~', $line)) {
                    $paramChunks = $this->getParamChunksFromLine($line);
                    
                    $paramType = array_shift($paramChunks);
                    $paramName = substr(array_shift($paramChunks), 1);
                    $params[$paramName] = [
                        'type'        => $paramType,
                        'name'        => $paramName,
                        'description' => implode(' ', $paramChunks),
                    ];
                }
            }
        }
        
        return [$title, $description, $params];
    } // end getRouteDocBlock
    
    private function getParamChunksFromLine($line)
    {
        $paramChunks = explode(' ', $line);
        $paramChunks = array_filter($paramChunks, function($val) {
            return $val !== '';
        });
        unset($paramChunks[0]);
        
        return $paramChunks;
    } // end getParamChunksFromLine
    
    private function filterDocBlock($docs)
    {
        foreach ($docs as &$line) {
            $line = preg_replace('~\s*\*\s*~', '', $line);
            $line = preg_replace('~^/$~', '', $line);
        }
        $docs = array_values(array_filter($docs));
        
        return $docs;
    } // end filterDocBlock
    
    private function generateEndpointKey($class)
    {
        $disabledNamespaces = $this->config->get('yaro.apidocs.disabled_namespaces', []);
        
        $chunks = explode('\\', $class);
        foreach ($chunks as $index => $chunk) {
            if (in_array($chunk, $disabledNamespaces)) {
                unset($chunks[$index]);
                continue;
            }
            
            $chunk = preg_replace('~Controller$~', '', $chunk);
            if ($chunk) {
                $chunk = $this->splitCamelCaseToWords($chunk);
                $chunks[$index] = implode(' ', $chunk);
            }
        }
           
        return implode('.', $chunks);
    } // end generateEndpointKey
    
    private function getSortedEndpoints($endpoints)
    {
        ksort($endpoints);

        $sorted = array();
        foreach ($endpoints as $key => $val) {
            $this->ins($sorted, explode('.', $key), $val);
        }
        
        return $sorted;
    } // end getSortedEndpoints
    
    private function getUriParams($route)
    {
        preg_match_all('~{(\w+)}~', $this->getRouteParam($route, 'uri'), $matches);
        
        return isset($matches[1]) ? $matches[1] : [];
    } // end getUriParams
    
    private function generateHashForUrl($key, $route, $method)
    {
        $path = preg_replace('~\s+~', '-', $key);
        $httpMethod = $this->getRouteParam($route, 'methods.0');
        $classMethod = implode('-', $this->splitCamelCaseToWords($method));
        
        $hash = $path .'::'. $httpMethod .'::'. $classMethod;
        
        return strtolower($hash);
    } // end generateHashForUrl
    
    private function splitCamelCaseToWords($chunk)
    {
        $splitCamelCaseRegexp = '/(?#! splitCamelCase Rev:20140412)
            # Split camelCase "words". Two global alternatives. Either g1of2:
              (?<=[a-z])      # Position is after a lowercase,
              (?=[A-Z])       # and before an uppercase letter.
            | (?<=[A-Z])      # Or g2of2; Position is after uppercase,
              (?=[A-Z][a-z])  # and before upper-then-lower case.
            /x';
            
        return preg_split($splitCamelCaseRegexp, $chunk);
    } // end splitCamelCaseToWords
    
    private function getRouteParam($route, $param)
    {
        $route = (array) $route;
        $prefix = chr(0).'*'.chr(0);
        
        return array_get(
            $route, 
            $prefix.$param, 
            array_get($route, $param)
        );
    } // end getRouteParam
    
    private function ins(&$ary, $keys, $val) 
    {
        $keys ? 
            $this->ins($ary[array_shift($keys)], $keys, $val) :
            $ary = $val;
    } // end ins
    
}
