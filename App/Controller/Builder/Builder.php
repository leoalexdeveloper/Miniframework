<?php

namespace App\Controller\Builder;

class Builder{
    private $httpMethod;
    private $controller;
    private $action;
    private $methodVisibility;
    private $builderMethod;
    private $routeFilePath;
    private $controllerFilePath;
    private $templateFilePath;
    private $pageBodyFilePath;
    private $pageHeadFilePath;
    private $pageFooterFilePath;
    private $cssFilePath;
    private $jsFilePath;
    private $methodPath;
    private $errors = [];
    private array $errorsIndex = [
        '0' => "The route exists!",
        '1' => "The route doesn't exists!",
        '2' => "Controller exists!",
        '3' => "Controller doesn't exists!",
        '4' => "Template exists!",
        '5' => "Template doesn't exists!",
        '6' => "Pages Body exists!",
        '7' => "Pages Body doesn't exists!",
        '8' => "Pages Head exists!",
        '9' => "Pages Head doesn't exists!",
        '10' => "Pages Footer exists!",
        '11' => "Pages Footer doesn't exists!",
        '12' => "Css exists!",
        '13' => "Css doesn't exists!",
        '14' => "Js exists!",
        '15' => "Js doesn't exists!",
        '16' => "Method exists!",
        '17' => "Method doens't exists!",
        '18' => "Thie controller structure not exists yet!",
        '19' => "This route has partially initiated!",
        '20' => "The structure for this route was already done!",
    ];

    public function __construct($uri){
        extract($uri);
        $uri = array_values(array_filter(explode("/", str_replace(SUB_FOLDER, "", $_SERVER['REQUEST_URI']))));
        $this->builderMethod = $uri[1] ? $uri[1] : $this->captureError('Missing Argunments: BuilderMethod / Http method / Controller / Action');
        $this->httpMethod = isset($uri[2]) ? strtoupper($uri[2]) : $this->captureError('Missing Argunments: BuilderMethod / Http method / Controller / Action');
        $this->controller = isset($uri[3]) ? ucfirst($uri[3]) : $this->captureError('Missing Argunments: BuilderMethod / Http method / Controller / Action');
        $this->action = isset($uri[4]) ? $uri[4] : $this->captureError('Missing Argunments: BuilderMethod / Http method / Controller / Action');
        $this->methodVisibility = (isset($uri[5])) ? $uri[5] : null;

        $this->routeFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Routes.txt';
        $this->controllerFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Controller/' . $this->controller . "/" . $this->controller . ".php";
        $this->templateFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/View/Templates/' . $this->controller . "/" . $this->controller . ".php";
        $this->pageBodyFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/View/Pages/' . $this->controller . "/Body.php";
        $this->pageHeadFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/View/Pages/' . $this->controller . "/Head.php";
        $this->pageFooterFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/View/Pages/' . $this->controller . "/Footer.php";
        $this->cssFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'Public/Css/' . $this->controller . "/" . $this->controller . ".css";
        $this->jsFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'Public/Js/' . $this->controller . "/" . $this->controller . ".js";
        $this->methodPath = '\\App\\Controller\\' . $this->controller . "\\" . $this->action;
    }

    private function captureError($error){
        throw new \Exception($error);
    }

    public function check_route(){
        $file = file_get_contents($this->routeFilePath);
        $pattern = $this->httpMethod . " " . $this->controller . "/" . $this->action . "/";
        
        foreach(explode("\n", $file) as $route){
            
            if(strcmp(trim($route), $pattern) === 0){
                $this->errors [] = 0;
                break;
            }
        }
        (!in_array(0, $this->errors)) ? $this->errors [] = 1 : $this->errors = $this->errors;
        $this->checkFolderAndFilesStructure();
    }

    private function checkFolderAndFilesStructure(){
        if(file_exists($this->controllerFilePath)){
            $this->errors [] = 2;
        }else{
            $this->errors [] = 3;
        }

        if(file_exists($this->templateFilePath)){
            $this->errors [] = 4;
        }else{
            $this->errors [] = 5;
        }

        if(file_exists($this->pageBodyFilePath)){
            $this->errors [] = 6;
        }else{
            $this->errors [] = 7;
        }

        if(file_exists($this->pageHeadFilePath)){
            $this->errors [] = 8;
        }else{
            $this->errors [] = 9;
        }

        if(file_exists($this->pageFooterFilePath)){
            $this->errors [] = 10;
        }else{
            $this->errors [] = 11;
        }

        if(file_exists($this->cssFilePath)){
            $this->errors [] = 12;
        }else{
            $this->errors [] = 13;
        }

        if(file_exists($this->jsFilePath)){
            $this->errors [] = 14;
        }else{
            $this->errors [] = 15;
        }
        
        $this->checkForClassMethod();
        $this->showStructureRelatory();
    }

    private function checkForClassMethod(){
        if(strpos(implode(",", $this->errors), 3) > -1){
            $reflection = new \ReflectionClass($this->methodPath);
            
            foreach($reflection->getMethods() as $method){
                
                if(strcmp(trim($method->name), $this->action) === 0){
                    $this->errors [] = 16;
                    return;
                }
            }
        }
        $this->errors [] = 17;
    }


    private function showStructureRelatory(){
        if(count($this->errors) === 0){
            echo "<pre>";
            echo $this->errorsIndex[18];
            echo "</pre>";
        }

        if(count($this->errors) > 0 && count($this->errors) <= 9 && strpos(implode("", $this->errors), "doesn't")){
            echo "<pre>";
            print_r($this->errors);
            foreach($this->errors as $index){
                echo($this->errorsIndex[$index]) . "<br>";
            }
            echo $this->errorsIndex[19];
            echo "</pre>";
        }
        
        if(count($this->errors) == 9 && !strpos(implode("", $this->errors), "doesn't")){
            echo "<pre>";
            foreach($this->errors as $index){ 
                echo($this->errorsIndex[$index]) . "<br>"; 
            }
            echo $this->errorsIndex[20];
            echo "</pre>";
        }
    }

    public function create_route(){
        $this->check_route();
        $this->addNewRoute();
    }

    private function addNewRoute(){
        if(array_search(1, $this->errors) > -1){
            echo "aqui";
            $filePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Routes.txt';
            $fileContent = file_get_contents($filePath);
            $fileContent = $fileContent . "\n" . $this->httpMethod . " " . lcfirst($this->controller) . "/" . $this->action . "/";
            file_put_contents($filePath, $fileContent);
            echo "Route created";
            $this->check_route();
        }
    }

    public function create_controller(){
        $this->check_route();
        $this->addNewController();
    }

    private function addNewController(){
        if(array_search(3, $this->errors) > -1){
            $filePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Controller_Default.txt';
            $fileContent = file_get_contents($filePath);
            $fileContent = str_replace(["{{{controller}}}", "{{{action}}}"], [$this->controller, lcfirst($this->controller)], $fileContent);
            mkdir(substr($this->controllerFilePath, 0, -strlen($this->controller . ".php")));
            file_put_contents($this->controllerFilePath, $fileContent);
            echo "Controller created";
            $this->check_route();
        }
    }

    public function create_method(){
        $this->check_route();
        $this->addNewMethod();
    }

    private function addNewMethod(){
        if(array_search(17, $this->errors) > -1 && isset($this->methodVisibility)){
            $fileContent = file_get_contents($this->controllerFilePath);
            $methodStructure = $this->methodVisibility . " function " . $this->action . "(){\n\n\t}\n}";
            $fileContent = substr($fileContent, 0, -1) . "\t" . $methodStructure;
            file_put_contents($this->controllerFilePath, $fileContent);
        }else{
            echo "verify the visibility attribute";
        }
    }

    public function delete_route(){
        $this->check_route();
        $this->deleteRoute();
    }

    private function deleteRoute(){
        $this->check_route();
        $filePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Routes.txt';
        $routeToBeDeleted = $this->httpMethod . " " . $this->controller . "/" . $this->action . "/";
        $fileContent = file_get_contents($filePath);
        $fileContent = str_replace($routeToBeDeleted, "", $fileContent);
        
        file_put_contents($filePath, trim($fileContent));
        $this->check_route();
    }
}