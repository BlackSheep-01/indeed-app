<?php

class Router
{
    protected $routes = [];

    /** 
     * routes = [ [.....] ,
     *            [ "method" => "GET",
     *              "uri" => "/listings",
     *              "controller" => "controllers/listings/index.php"
     *            ] ,
     *           [.....]
     *        ]
     */
    public function registerRoute($method, $uri, $controller){
        $this->routes[] = [         //add all routes (from routes.php) to array
            "method" => $method,
            "uri" => $uri,
            "controller" => $controller
        ];
    }

    /**
     * add route for GET request
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function get($uri, $controller){
        $this->registerRoute("GET", $uri, $controller);
    }

    /**
     * add route for POST request
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function post($uri, $controller) {
        $this->registerRoute("POST", $uri, $controller);
    }

    /**
     * add route for PUT request
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function put($uri, $controller) {
        $this->registerRoute("PUT", $uri, $controller);
    }

    /**
     * add route for DELETE request
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function delete($uri, $controller) {
        $this->registerRoute("DELETE", $uri, $controller);
    }

    /**
     * load error page
     * @param int $httpCode
     * @return void
     */
    public function error($httpCode = 404){
        http_response_code($httpCode);
        loadView("error/{$httpCode}");
        exit;
    }

    /**
     * route the request
     * @param string $uri
     * @param string $method
     * @return void
     */
    public function route($method, $uri){
        foreach($this->routes as $route){
            if($route["method"] === $method && $route["uri"] === $uri){
                require basePath("App/" . $route["controller"]);    
                return;
            }
        }
        $this->error();   //if no match in routes array 
    }
}