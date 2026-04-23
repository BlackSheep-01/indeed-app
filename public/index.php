<?php
require "../helpers.php";
// require basePath("Framework/Database.php");
// require basePath("Framework/Router.php");


spl_autoload_register(function($class){
    $path = basePath("Framework/" . $class . ".php");
    if(file_exists($path)){
        require $path;
    }
});

/* database */
$config = require basePath("config/db.php");   //array
$db = new Database($config);    


/* router */
$router = new Router();
$routes = require basePath("routes.php");     //?             


/* user request */
$method = $_SERVER["REQUEST_METHOD"];       //returns the http method: GET/POST/PUT/DELETE 
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);       //returns url path without query strings

/* response to user request via routing */
$router->route($method, $uri);   
