<?php

/**
 * get absolute path of any file
 * @param string $path
 * @return string
 */
function basePath($path= ""){
    return __DIR__ . "/" . $path;       //local hosting _DIR_ : c/xampp/htdocs/job_portal
}

/**
 * load a view
 * @param string $name
 * @return void
 */
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
/* extract(): build in function which takes an associative array and converts only its topmost level key:val pairs into variables and corresponding values 
   eg:  [ 'listings'=>[assoc arr....] ]  -> extract() ->   $listings=[assoc arr..]  */

/**
 * load a partial
 * @param string $name
 * @return void
 */
function loadPartial($name, $data= []){
    $partialPath = basePath("App/views/partials/{$name}.php");
    if (file_exists($partialPath)) {
        extract($data);
        require $partialPath;
    } else {
        echo "View not found";
    }
}

/**
 * sanitize user input data
 * @param string $dirty
 * @return string
 */
function sanitize($dirty){
    return filter_var(trim($dirty), FILTER_SANITIZE_SPECIAL_CHARS);
}

/**
 * redirect to given url
 * @param string $url
 * @return void
 */
function redirect($url){
    header("Location: {$url}");
    exit;
}


/**
 * print values
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