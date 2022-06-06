<?php
class Route {
    private $url;
    private $verb;
    private $controller;
    private $method;
    private $params;
    public function __construct($url, $verb, $controller, $method){
        $this->url = $url;
        $this->verb = $verb;
        $this->controller = $controller;
        $this->method = $method;
        $this->params = [];
    }
    public function match($url, $verb) {
        if($this->verb != $verb){
            return false;
        }
        $partsURL = explode("/", trim($url,'/'));
        $partsRoute = explode("/", trim($this->url,'/'));
        if(count($partsRoute) != count($partsURL)){
            return false;
        }
        foreach ($partsRoute as $key => $part) {
            if($part[0] != ":"){
                if($part != $partsURL[$key])
                return false;
            } //es un parametro
            else
            $this->params[$part] = $partsURL[$key];
        }
        return true;
    }
    public function run(){
        $controller = $this->controller;  
        $method = $this->method;
        $params = $this->params;
       
        (new $controller())->$method($params);
    }
}
class Router{
    private $routeTable = [];
    
    public function route($url, $verb) {
        //$ruta->url //no compila!
        foreach ($this->routeTable as $route) {
            if($route->match($url, $verb)){
                //TODO: ejecutar el controller//ejecutar el controller
                // pasarle los parametros
                $route->run();
                return;
            }
            else if($route->match("not-found", "GET")){
                $route->run();
                return;
            }
        }
    }
    
    public function addRoute ($url, $verb, $controller, $method) {
        $this->routeTable[] = new Route($url, $verb, $controller, $method);
    }
    
    
}