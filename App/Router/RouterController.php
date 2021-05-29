<?php

namespace App\Router;

class RouterController{
    private string $uri;
    private string $method;
    private string $usableUri;
    private array $routes;
    private string $controllerPath;
    private string $controller;
    private string $action;

    public function __construct(){
        try{
            $this->uri = $this->getUri();
            $this->method = $this->getMethod();
            $this->usableUri = $this->getUsableUri();
            REQUIRE_ONCE("Routes.php");
            $this->controllerPath = $this->validateRoute();
            $this->executeRoute();
        }catch(\Exception $e){
            echo $e->getMessage();
        }
    }
    
    private function getUri() : String 
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    private function getMethod() : String 
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    private function getUsableUri() : String 
    {
        
        $this->uri = str_replace(SUB_FOLDER, "", $this->uri);
    
        if(substr($this->uri, -1) !== "/"){
            $this->uri = $this->uri . "/";
        } 
        
        if(substr_count($this->uri, "/", 0) === 1){
            $this->uri = $this->uri . $this->uri;
        }

        return $this->uri;
    }

    private function validateRoute()
    {
        $uriExploded = explode("/", $this->uri);
        $this->controller = $uriExploded[0];
        $this->action = $uriExploded[1];
        
        foreach($this->routes as $route){
            
            if(['method'=>$this->method, 'route'=>$this->controller . "/" . $this->action . "/"] === $route
            && 
            file_exists(dirname(__DIR__) . "/Controller" . "/" . $this->controller . "/" . $this->controller . ".php"))
            {
                return "\\App\\Controller\\" . ucfirst($this->controller) . "\\" . $this->controller;
            }
        }
        throw new \Exception("Page not found");
    }

    private function executeRoute() : Void
    {
        $uri = [
            "controller" => $this->controller,
            "action" => $this->action
        ];
        
        (new $this->controllerPath($uri))->{$this->action}();
    }
}