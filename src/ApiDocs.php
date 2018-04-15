<?php 

namespace Yaro\ApiDocs;

use ReflectionClass;
use Illuminate\Routing\Router;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Http\Request;
use Yaro\ApiDocs\Blueprint;

class ApiDocs
{
    
    private $router;
    private $config;
    private $request;
    
    public function __construct(Router $router, Config $config, Request $request)
    {
        $this->router  = $router;
        $this->config  = $config;
        $this->request = $request;
    } // end __construct

    public function show($routePrefix = null)
    {
        $currentPrefix = $this->request->get('prefix', $this->getRoutePrefix($routePrefix));
        $endpoints = $this->getEndpoints($currentPrefix);
        $endpoints = $this->getSortedEndpoints($endpoints);
        $prefixes  = $this->getRoutePrefixes();
        
        return view('apidocs::docs', compact('endpoints', 'prefixes', 'currentPrefix'));
    } // end show

    public function blueprint($routePrefix = null)
    {
        $routePrefix = $this->request->get('prefix', $this->getRoutePrefix($routePrefix));
        
        $blueprint = app()->make(Blueprint::class);
        $blueprint->setRoutePrefix($routePrefix);
        $blueprint->setEndpoints($this->getEndpoints($routePrefix));
        
        return $blueprint;
    } // end blueprint
    
    private function getEndpoints($routePrefix)
    {
        $endpoints = [];

        foreach ($this->router->getRoutes() as $route) {
            if (!$this->isPrefixedRoute($route, $routePrefix) || $this->isClosureRoute($route) || $this->isExcluded($route)) {
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
    
    private function isPrefixedRoute($route, $routePrefix)
    {
        $regexp = '~^'. preg_quote($routePrefix) .'~';
        
        return preg_match($regexp, $this->getRouteParam($route, 'uri'));
    } // end isPrefixedRoute
    
    private function getRoutePrefix($routePrefix)
    {
        $prefixes = $this->getRoutePrefixes();
        
        return in_array($routePrefix, $prefixes) ? $routePrefix : array_shift($prefixes);
    }
    
    private function getRoutePrefixes()
    {
        $prefixes = $this->config->get('yaro.apidocs.prefix', 'api');
        if (!is_array($prefixes)) {
            $prefixes = [$prefixes];
        }
        
        return $prefixes;
    }
    
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

        $reflectorMethod = $reflector->getMethod($method);
        $docs = explode("\n", $reflectorMethod->getDocComment());
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
                        'template'    => $this->getParamTemplateByType($paramType),
                    ];
                }
            }
        }

        // TODO:
        $rules = [];
        foreach($reflectorMethod->getParameters() as $reflectorParam) {
            $paramClass = $reflectorParam->getClass();
            if ($paramClass instanceof ReflectionClass) {
                $name = $paramClass->getName();
                $paramClassInstance = new $name;
                if (is_a($paramClassInstance, Request::class) && method_exists($paramClassInstance, 'rules')) {
                    $paramClassInstance->__apidocs = true;
                    $rules = $paramClassInstance->rules();
                }
            }
        }

        $params = $this->fillParamsWithRequestRules($params, $rules);

        foreach ($params as $name => $param) {
            $params[$name]['rules'] = $rules[$name] ?? [];
            if (is_string($params[$name]['rules'])) {
                $params[$name]['rules'] = explode('|', $params[$name]['rules']);
            } elseif (is_array($params[$name]['rules'])) {
                $params[$name]['rules'] = $params[$name]['rules'];
            }
        }

        return [$title, $description, $params];
    } // end getRouteDocBlock

    private function fillParamsWithRequestRules($params, $rules)
    {
        foreach ($rules as $paramName => $rule) {
            if (isset($params[$paramName])) {
                continue;
            }

            $params[$paramName] = [
                'type'        => 'string',
                'name'        => $paramName,
                'description' => '',
                'template'    => $this->getParamTemplateByType('string'),
            ];
        }

        return $params;
    }
    
    private function getParamTemplateByType($paramType)
    {
        switch ($paramType) {
            case 'file':
                return 'file';
                
            case 'bool':
            case 'boolean':
                return 'boolean';
                
            case 'int':
                return 'integer';
                
            case 'text':
            case 'string':
            default:
                return 'string';
        }
    } // end getParamTemplateByType
    
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
        
        return $this->arrayGet(
            $route, 
            $prefix.$param, 
            $this->arrayGet($route, $param)
        );
    } // end getRouteParam
    
    private function ins(&$ary, $keys, $val) 
    {
        $keys ? 
            $this->ins($ary[array_shift($keys)], $keys, $val) :
            $ary = $val;
    } // end ins
    
    private function arrayGet($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }
        
        if (isset($array[$key])) {
            return $array[$key];
        }
        
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || ! array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }
        
        return $array;
    }
    
}
