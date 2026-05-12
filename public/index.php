<?php
require "../helpers.php";
require __DIR__ . "/../vendor/autoload.php";

use Framework\Database;
use Framework\Router;
use Framework\Session;

Session::start();


/* database */
$config = require basePath("config/db.php");   //array
new Database($config);    //


/* router */
$router = new Router();
require basePath("routes.php");     //all available routes registered          


/* browser request */
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);       //returns uri path without query strings

/* response to request via routing */
$router->route($uri);   
