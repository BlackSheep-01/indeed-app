<?php

// All the avalable routes for the project are listed

$router->get("/", "controllers/home.php");   

$router->get("/listings", "controllers/listings/index.php");

$router->get("/listings/create", "controllers/listings/create.php");

$router->get("/listing", "controllers/listings/show.php");   //each listing details
