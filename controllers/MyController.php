<?php

class MyController {

    protected $_viewParams;
    protected $_requestParams;

    public function __construct() {
        $this->init();
    }

    public function init() {
        
    }

    public function setViewValue($k, $v, $encoded = false) {
        if ($encoded) {
            $v = htmlentities($v);
        }
        $this->_viewParams[$k] = $v;
    }

    public function setRequestParams(array $pairs) {
        $this->_requestParams = $pairs;
    }

    public function getRequestParam($k, $default = null) {
        if (isset($this->_requestParams[$k])) {
            return $this->_requestParams[$k];
        }
        return $default;
    }

    public function renderView($templateName, $controllerName = null, $encoded = true) {
        $templateFolder = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'view';
        if (isset($controllerName)) {
            $templateFolder .= DIRECTORY_SEPARATOR . $controllerName;
        }
        $fullPath = $templateFolder . DIRECTORY_SEPARATOR . $templateName . '.php';
        if (!file_exists($fullPath)) {
            return false;
        }
        $content = file_get_contents($fullPath);
        if (!empty($this->_viewParams)) {
            $keys = array_keys($this->_viewParams);
            foreach ($keys as $i => $key) {
                $keys[$i] = '{$' . $key . '}';
            }
            $content = str_replace($keys, array_values($this->_viewParams), $content);
        }

        if ($encoded === false) {
            return $content;
        } else {
            echo $content;
        }
    }

    public function render() {
        $this->setViewValue('pageTitle', 'Home');
        $actionMethod = ucfirst($this->getRequestParam('action', 'index')) . 'Action';
        $this->$actionMethod();
        $this->setViewValue('base_folder', APPLICATION_FOLDER);


        //Create 'top' part of site (header)
        $this->renderView('header');

        //Create controller-view (content)
        $this->renderView($this->getRequestParam('action', 'index'), $this->getRequestParam('controller', 'index'));

        //Create 'bottom' part of site (footer)
        $this->renderView('footer');
    }

}
