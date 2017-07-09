<?php 

namespace Yaro\ApiDocs;

use ReflectionClass;
use Illuminate\Routing\Router;

class ApiDocs
{
    
    private $router;
    
    public function __construct(Router $router)
    {
        $this->router = $router;
    } // end __construct

    public function show()
    {
        $endpoints = $this->getEndpoints();
        
        return view('apidocs::docs', compact('endpoints'));
    } // end show
    
    private function getEndpoints()
    {
        $disabledNamespaces = config('yaro.apidocs.disabled_namespaces', []);
        $prefix = config('yaro.apidocs.prefix', 'api');
        
        $endpoints = [];
        
        $splitCamelCaseRegexp = '/(?#! splitCamelCase Rev:20140412)
            # Split camelCase "words". Two global alternatives. Either g1of2:
              (?<=[a-z])      # Position is after a lowercase,
              (?=[A-Z])       # and before an uppercase letter.
            | (?<=[A-Z])      # Or g2of2; Position is after uppercase,
              (?=[A-Z][a-z])  # and before upper-then-lower case.
            /x';
        
        foreach ($this->router->getRoutes() as $route) {
            if (!preg_match('~^'. preg_quote($prefix) .'~', $route->uri)) {
                continue;
            }
            
            $action = $route->action['uses'];
            if (is_object($action)) {
                continue;
            }
            
            $array = explode("@", $route->action['controller']);
            $class = $array[0];
            $method = $array[1];
            
            if (!class_exists($class) || !method_exists($class, $method)) {
                continue;
            }
            
            $reflector = new ReflectionClass($class);
            
            $docs = explode("\n", $reflector->getMethod($method)->getDocComment());
            $docs = array_filter($docs);
            if (!$docs) {
                continue;
            }
            
            foreach ($docs as &$line) {
                $line = preg_replace('~\s*\*\s*~', '', $line);
                $line = preg_replace('~/~', '', $line);
            }
            $docs = array_values(array_filter($docs));
            
            $title = array_shift($docs);
            $description = '';
            $params = [];
            $checkForLongDescription = true;
            foreach ($docs as $line) {
                if ($checkForLongDescription && !preg_match('~^@\w+~', $line)) {
                    $description .= trim($line) .' ';
                } elseif (preg_match('~^@\w+~', $line)) {
                    $checkForLongDescription = false;
                    if (preg_match('~^@param~', $line)) {
                        $paramChunks = explode(' ', $line);
                        $paramChunks = array_filter($paramChunks, function($val) {
                            return $val !== '';
                        });
                        unset($paramChunks[0]);
                        
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
            
            
            $chunks = explode('\\', $class);
            foreach ($chunks as $index => $chunk) {
                if (in_array($chunk, $disabledNamespaces)) {
                    unset($chunks[$index]);
                    continue;
                }
                
                $chunk = preg_replace('~Controller$~', '', $chunk);
                if ($chunk) {
                    $chunk = preg_split($splitCamelCaseRegexp, $chunk);
                    $chunks[$index] = implode(' ', $chunk);
                }
                
            }
            if (in_array($chunk, $disabledNamespaces)) {
                unset($chunks[$index]);
            }
               
            $key = implode('.', $chunks);
            
            preg_match_all('~{(\w+)}~', $route->uri, $matches);
            $uriParams = isset($matches[1]) ? $matches[1] : [];
            
            $endpoints[$key][] = [
                'hash'    => strtolower(preg_replace('~\s+~', '-', $key) .'::'. $route->methods[0] .'::'. implode('-', preg_split($splitCamelCaseRegexp, $method))),
                'uri'     => $route->uri,
                'name'    => $method,
                'methods' => $route->methods,
                'docs' => [
                    'title'       => $title, 
                    'description' => trim($description), 
                    'params'      => $params,
                    'uri_params'  => $uriParams,
                ],
            ];
        }
        
        ksort($endpoints);
    
        $sorted = array();
        foreach($endpoints as $key => $val) {
            ins($sorted, explode('.', $key), $val);
        }
        return $sorted;
    } // end getEndpoints
    
}
