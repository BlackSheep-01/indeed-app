<?php

$config = require basePath("config/db.php");
$db = new Database($config);   

$listings = $db->query("SELECT * FROM listings LIMIT 6")->fetchAll();


loadView("home", [
    'listings' => $listings
]);
// [ 'listings'=>[assoc arr....] ] converted to:  $listings=[assoc arr..]  via extract()