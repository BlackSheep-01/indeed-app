<?php

/**
 * Get the absolute path of any file
 * @param string $path
 * @return string
 */
function basePath($path= ""){
    return __DIR__ . "/" . $path;       //local hosting _DIR_ : c/xampp/htdocs/job_portal
}

/**
 * Load a view
 * @param string $name
 * @return void
 */
/* extract(): build in function which takes an associative array and converts only its topmost level key:val pairs into variables and corresponding values */
function loadView($name, $data= []){
    $viewPath = basePath("App/views/{$name}.view.php");
    if(file_exists($viewPath)){
        extract($data);
        require $viewPath;
    }
    else{
        echo "View not found";
    }
}

/**
 * Load a partial
 * @param string $name
 * @return void
 */
function loadPartial($name){
    require basePath("App/views/partials/{$name}.php");
}

/**
 * Print values
 * @param mixed $value
 * @return void
 */
function inspect($value){
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
}

function inspectAndDie($value){
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    die();
}



