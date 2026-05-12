<?php

$router->get("/", "HomeController@index");

// Listings
$router->get("/listings", "ListingController@index");
$router->get("/listings/create", "ListingController@create", ["auth"]);
$router->get("/listings/search", "ListingController@search");   
$router->get("/listings/edit/{id}", "ListingController@edit", ["auth"]);
$router->get("/listings/{id}", "ListingController@show");

$router->post("/listings", "ListingController@store", ["auth"]);

$router->put("/listings/{id}", "ListingController@update", ["auth"]);

$router->delete("/listings/{id}", "ListingController@destroy", ["auth"]);


// User
$router->get("/auth/register", "UserController@create", ["guest"]);
$router->get("/auth/login", "UserController@login", ["guest"]);

$router->post("/auth/register", "UserController@store", ["guest"]);
$router->post("/auth/login", "UserController@authenticate", ["guest"]);
$router->post("/auth/logout", "UserController@logout", ["auth"]);


/* if uri has same initial path & belong to same method group, order of routes important. static first, dynamic later because routes.php is traversed from top to bottom*/
