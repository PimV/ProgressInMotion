<?php

define('APPLICATION_PATH', getcwd());
define('APPLICATION_FOLDER', "/ProgressInMotion/");

/**
 * Autoloads classes by filename. Checks whether the file exists and if so, it
 * loads the class.
 * @param type $name The filename requested
 */
function __autoload($name) {
    $paths = array(
        APPLICATION_PATH . DIRECTORY_SEPARATOR . 'view',
        APPLICATION_PATH . DIRECTORY_SEPARATOR . 'models',
        APPLICATION_PATH . DIRECTORY_SEPARATOR . 'controllers',
    );

    foreach ($paths as $path) {
        $filePath = $path . DIRECTORY_SEPARATOR . $name . '.php';
        if (file_exists($filePath)) {
            require $filePath;
            break;
        }
    }
}

//Start a new session upon entering the site.
session_start();

//Request the routing
$uri = $_SERVER['REQUEST_URI'];
$uri = str_replace(APPLICATION_FOLDER, '', $uri);

//Check which controller we are going to use
$uriSplit = explode('/', $uri, 3);
$controllerName = ucfirst($uriSplit[0]) . "Controller";

//Check check which action we are going to use
if (count($uriSplit) == 1) {
    $actionName = "IndexAction";
} else {
    $actionName = $uriSplit[1] . "Action";
}

//Check which request parameters we need to store (e.a. controller/action/page/1
//will give the request paramater with key 'page' and value '1'
$pairs = array();
if (count($uriSplit) == 3) {
    $paramSplit = explode('/', $uriSplit[2]);
    foreach ($paramSplit as $i => $value) {
        if ($i % 2 == 0) {
            $pairs[$value] = null;
        } else {
            $pairs[$previousKey] = $value;
        }
        $previousKey = $value;
    }
}

//Create the controller which we found 2 blocks up
if (file_exists('controllers' . DIRECTORY_SEPARATOR . $controllerName . '.php')) {
    include_once('controllers' . DIRECTORY_SEPARATOR . $controllerName . '.php');
} else {
    //Check if user entered the url (so not by redirection)
    if (!empty($uri)) {
        header('Location: ' . APPLICATION_FOLDER);
    }
}

//Check if the controller class exists. If not found, go to default IndexController.
if (!class_exists($controllerName)) {
    $controllerName = "IndexController";
    include_once('controllers' . DIRECTORY_SEPARATOR . $controllerName . '.php');
}
$controller = new $controllerName();

//Check if we have to do an action. If so, check whether it exists or not. If it
//does not exist, refer to default main.
if (empty($actionName)) {
    $actionName = "IndexAction";
} else if (!method_exists($controller, $actionName)) {
    header('Location: ' . APPLICATION_FOLDER);
}

//Populate more request parameters
$pairs['controller'] = lcfirst(str_replace('Controller', '', $controllerName));
$pairs['action'] = lcfirst(str_replace('Action', '', $actionName));
$controller->setRequestParams($pairs);
$controller->render();





