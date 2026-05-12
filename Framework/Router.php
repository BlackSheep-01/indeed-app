<?php
namespace Framework;

use App\Controllers\ErrorController;
use Framework\Middleware\Authorize;


class Router
{
    protected $routes = [];

    /**
     * @param string $uri
     * @param string $method
     * @param string $action
     * @param array $middleware
     * @return void
     */
    public function registerRoute($method, $uri, $action, $middleware= []){
        list($controller, $controllerMethod) = explode("@", $action);    //eg: "ListingController@update"  ->  ["ListingController", "update"]  ->  $controller= "ListingController", $controllerMethod= "update" 
        $this->routes[] = [         //add all routes (from routes.php) to array
            "method" => $method,
            "uri" => $uri,
            "controller" => $controller,
            "controllerMethod" => $controllerMethod,
            "middleware" => $middleware
        ];
    }
    /*
    routes = [ [.....] ,
                [ "method" => "PUT",
                  "uri" => "/listings/{id}",
                  "controller" => "ListingController",
                  "controllerMethod" => "update",
                  "middleware" => ["auth"]
                ] 
            ]
    */


    /**
     * add Get route
     * @param string $uri
     * @param string $action
     * @param array $middleware
     * @return void
     */
    public function get($uri, $action, $middleware= []){
        $this->registerRoute("GET", $uri, $action, $middleware);
    }

    /**
     * add Post route
     * @param string $uri
     * @param string $action
     * @param array $middleware
     * @return void
     */
    public function post($uri, $action, $middleware = []) {
        $this->registerRoute("POST", $uri, $action, $middleware);
    }

    /**
     * add Put route
     * @param string $uri
     * @param string $action
     * @param array $middleware
     * @return void
     */
    public function put($uri, $action, $middleware = []) {
        $this->registerRoute("PUT", $uri, $action, $middleware);
    }

    /**
     * add Delete route
     * @param string $uri
     * @param string $action
     * @param array $middleware
     * @return void
     */
    public function delete($uri, $action, $middleware = []) {
        $this->registerRoute("DELETE", $uri, $action, $middleware);
    }

    
    /**
     * route the request
     * @param string $uri
     * @return void
     */
    public function route($uri){
        $requestMethod = $_SERVER["REQUEST_METHOD"];       //returns the http method: GET/POST/PUT/DELETE req
        if($requestMethod === "POST"  &&  isset($_POST["_method"])){      //forms only support GET and POST, so we use hidden input _method to override the method for PUT and DELETE requests
            $requestMethod = strtoupper($_POST["_method"]);
        }
        foreach($this->routes as $route){
            $uriSegments = explode("/", trim($uri, "/"));       // "/listing/3"  ->  ["listing", "3"]
            $routeSegments = explode("/", trim($route["uri"], "/"));      // "/listing/{id}"  ->  ["listing", "{id}"]
            if(count($uriSegments) === count($routeSegments)  &&  strtoupper($route["method"]) === $requestMethod){
                $params = [];
                $match = true;
                for ($i = 0; $i < count($uriSegments); $i++) {
                    $uriPart = $uriSegments[$i];
                    $routePart = $routeSegments[$i];
                    if (str_starts_with($routePart, "{")  &&  str_ends_with($routePart, "}")) {    // check if it's a parameter like {id}
                        $paramName = trim($routePart, "{}");    // "{id}" -> "id"
                        $params[$paramName] = $uriPart;        // $params["id"] = "3"
                    }
                    elseif ($uriPart !== $routePart) {         // otherwise it must match exactly eg: "listing" === "listing" 
                        $match = false;
                        break;
                    }
                }
                if($match){
                    foreach($route["middleware"] as $role){     // maybe "middleware" => ["auth", "admin", ...]
                        $authorize = new Authorize();
                        $authorize->handle($role);        // Middleware check
                    }
                    $controller = "App\\Controllers\\" . $route["controller"];
                    $controllerMethod = $route["controllerMethod"];
                    $controllerObj = new $controller();
                    if ( !empty($params))
                        $controllerObj->$controllerMethod($params);
                    else
                        $controllerObj->$controllerMethod();
                    return;
                }
            }
        }
        ErrorController::notFound();    //if route doesnt match
    }
}