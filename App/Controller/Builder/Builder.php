<?php

namespace App\Controller\Builder;

class Builder{
    private $httpMethod;
    private $controller;
    private $action;
    private $methodVisibility;
    private $comments;
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
        '1' => "Set Route first!",
        '2' => "Controller exists!",
        '3' => "Set Controller first!",
        '4' => "Template exists!",
        '5' => "Set Template first!",
        '6' => "Pages, Body Head and Footer exists!",
        '7' => "Set Pages Body first!",
        '8' => "Pages, Body Head and Footer exists!",
        '9' => "Set Pages Head first!",
        '10' => "Pages, Body Head and Footer exists!",
        '11' => "Set Pages Footer first!",
        '12' => "Css exists!",
        '13' => "Set Css first!",
        '14' => "Js exists!",
        '15' => "Set Js first!",
        '16' => "Action exists!",
        '17' => "Set Action first!",
        '18' => "Thie controller structure not exists yet!",
        '19' => "This route has partially initiated!",
        '20' => "The structure for this route was already done!",
        '21' => 'Missing Argunments: BuilderMethod / Http method / Controller / Action',
        '22' => 'Visibility not passed',
        '23' => 'Action not passed',
        '24' => 'Visibility must be public, protected or private',
    ];

    public function __construct($uri){
        extract($uri);
        $uri = array_values(array_filter(explode("/", str_replace(SUB_FOLDER, "", $_SERVER['REQUEST_URI']))));
        $this->builderMethod = $uri[1] ? $uri[1] : $this->throwError(21);
        $this->httpMethod = isset($uri[2]) ? urldecode(strtoupper($uri[2])) : $this->throwError(21);
        $this->controller = isset($uri[3]) ? $uri[3] = urldecode(ucfirst($uri[3])) : $this->throwError(21);
        $this->action = isset($uri[4]) ? $uri[4] = urldecode($uri[4]) : null;
        $this->methodVisibility = (isset($uri[5])) ? urldecode($uri[5]) : null;
        $this->comments = isset($uri[6]) ? $uri[6] = ucfirst(urldecode($uri[6])) : null;

        $this->routeFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Routes.txt';
        $this->controllerFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Controller/' . $this->controller . "/" . $this->controller . ".php";
        $this->templateFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/View/Templates/' . $this->controller . "/" . $this->controller . ".php";
        $this->pageBodyFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/View/Pages/' . $this->controller . "/Body.php";
        $this->pageHeadFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/View/Pages/' . $this->controller . "/Head.php";
        $this->pageFooterFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/View/Pages/' . $this->controller . "/Footer.php";
        $this->cssFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'Public/Css/' . $this->controller . "/" . $this->controller . ".css";
        $this->jsFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'Public/Js/' . $this->controller . "/" . $this->controller . ".js";
        $this->methodPath = '\\App\\Controller\\' . $this->controller . "\\" . $this->controller;

        $this->check_route();
        $this->showStructureRelatory();
    }

    private function throwError($error){
        throw new \Exception($this->errorsIndex[$error]);
    }

    public function check_route(){
        $file = file_get_contents($this->routeFilePath);
        $pattern = $this->httpMethod . " " . lcfirst($this->controller) . "/" . $this->action . "/";
        $file = explode("\n", $file);
        foreach($file as $route){
            if(strcmp(trim($route), $pattern) === 0){
                $this->errors [] = 0;
            }
        }
        (in_array(0, $this->errors) === false) ? $this->errors [] = 1 : $this->errors = $this->errors;
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
        
    }

    private function checkForClassMethod(){
        
        if(in_array(2, $this->errors) && isset($this->action)){
            
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
            
            foreach($this->errors as $index){
                echo($this->errorsIndex[$index]) . "<br>";
            }
            echo $this->errorsIndex[19];
            echo "</pre>";
        }
        
        if(count($this->errors) == 9){
            $sucsessMessages = [];
            echo "<pre>";
            
            foreach($this->errors as $index){
                echo($this->errorsIndex[$index]) . "<br>";
                if(strpos($this->errorsIndex[$index], 'Set') === false){
                    $sucsessMessages [] = $index;
                }
            }

            if(count($sucsessMessages) === count($this->errors)){
                echo "<br>" . $this->errorsIndex[20];
                die();
            }

            echo "</pre>";
        }
    }

    public function create_route(){
        
        $this->addNewRoute();
        header('Location:builder');
    }

    private function addNewRoute(){
        
        if(in_array(1, $this->errors)){
            $filePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Routes.txt';
            $fileContent = file_get_contents($filePath);
            $fileContent = $fileContent . "\n" . $this->httpMethod . " " . lcfirst($this->controller) . "/" . $this->action . "/";
            file_put_contents($filePath, $fileContent);
            echo "Route created";
            $this->check_route();
        }else{
            $this->throwError(0);
        }
    }

    public function create_controller(){
        
        $this->addNewController();
        header('Location:builder');
    }

    private function addNewController(){
        if(in_array(1, $this->errors))
        {
            $this->throwError(1);
        }
        else if(in_array(3, $this->errors)){
            $filePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Controller_Default.txt';
            $fileContent = file_get_contents($filePath);
            $fileContent = str_replace(["{{{controller}}}", "{{{action}}}", "{{{comments}}}"], [$this->controller, lcfirst($this->controller), $this->comments], $fileContent);
            mkdir(substr($this->controllerFilePath, 0, -strlen($this->controller . ".php")));
            file_put_contents($this->controllerFilePath, $fileContent);
            echo "Controller created";
            $this->check_route();
        }else{
            $this->throwError(2);
        }
    }

    public function create_method(){
        
        $this->addNewMethod();
        header('Location:builder');
    }

    private function addNewMethod(){
        if(in_array(1, $this->errors))
        {
            $this->throwError(1);
            
        }else if(in_array(3, $this->errors))
        {
            $this->throwError(3);
        }
        else if(!isset($this->action))
        {
            $this->throwError(23);
        }
        else if(!isset($this->methodVisibility))
        {
            $this->throwError(22);
        }
        else if(strcmp($this->methodVisibility, trim("private", '"')) !== 0  
            && 
            strcmp($this->methodVisibility, trim("public", '"')) !== 0 
            &&
            strcmp($this->methodVisibility, trim("protected", '"')) !== 0)
        {  
            $this->throwError(24);
        }
        else if(in_array(17, $this->errors) && in_array(0, $this->errors) && in_array(2, $this->errors))
        {
            
            $fileContent = trim(file_get_contents($this->controllerFilePath));
            
            $methodStructure = "\n\t/*\n\t\t" . $this->comments . "\n\t*/\n\t" . $this->methodVisibility . " function " . $this->action . "(){\n\n\t}\n}";
            $fileContent = substr($fileContent, 0, -1) . "\t" . $methodStructure;
            file_put_contents($this->controllerFilePath, $fileContent);
            $this->check_route();
            echo "Method was created!";  
            $this->check_route(); 
        }else{
           
            $this->throwError(16);
        }
    }

    public function create_template(){
        
        $this->addNewTemplate();
        header('Location:builder');
    }

    private function addNewTemplate(){
        if(in_array(1, $this->errors))
        {
            $this->throwError(1);
        }
        else if(in_array(3, $this->errors))
        {
            $this->throwError(3);
        }
        else if(in_array(17, $this->errors))
        {
            $this->throwError(17);
        }
        else if(in_array(5, $this->errors)){
            $templateDefaultPath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Template_Default.txt';
            
            $fileContent = file_get_contents($templateDefaultPath);
            if(!is_dir(substr($this->templateFilePath, 0, -strlen($this->controller . ".php")))){
                mkdir(substr($this->templateFilePath, 0, -strlen($this->controller . ".php")));
            }else{
                $this->throwError(4);
            }

            file_put_contents($this->templateFilePath, $fileContent);
            echo "Template was created!";
            $this->check_route();
            
        }else{
            $this->throwError(4);
        }
    }


    public function create_page(){
        
        $this->addNewPage();
        header('Location:builder');
    }

    private function addNewPage(){
        if(in_array(1, $this->errors))
        {
            $this->throwError(1);
        }
        else if(in_array(3, $this->errors))
        {
            $this->throwError(3);
        }
        else if(in_array(17, $this->errors))
        {
            $this->throwError(17);
        }else if(in_array(5, $this->errors))
        {
            $this->throwError(5);
        }
        else if(in_array(7, $this->errors)){
            $headDefaultPath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Head_Default.txt';
            $bodyDefaultPath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Body_Default.txt';
            $footerDefaultPath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Footer_Default.txt';
            $fileContentHead = file_get_contents($headDefaultPath);
            $fileContentBody = file_get_contents($bodyDefaultPath);
            $fileContentFooter = file_get_contents($footerDefaultPath);
            if(!is_dir(substr($this->pageHeadFilePath, 0, -strlen("Head.php")))){
                mkdir(substr($this->pageHeadFilePath, 0, -strlen("Head.php")));
            }
            
            if(!is_dir(substr($this->pageBodyFilePath, 0, -strlen("Body.php")))){
                mkdir(substr($this->pageBodyFilePath, 0, -strlen("Body.php")));
            }

            if(!is_dir(substr($this->pageFooterFilePath, 0, -strlen("Footer.php")))){
                mkdir(substr($this->pageFooterFilePath, 0, -strlen("Footer.php")));
            }

            file_put_contents($this->pageHeadFilePath, $fileContentHead);
            file_put_contents($this->pageBodyFilePath, $fileContentBody);
            file_put_contents($this->pageFooterFilePath, $fileContentFooter);
            echo "Head, Body and Footer was created!";
        }else{
            $this->throwError(6);
        }
    }

    public function create_css(){
        
        $this->addNewCss();
        header('Location:builder');
    }

    private function addNewCss(){
        if(in_array(1, $this->errors))
        {
            $this->throwError(1);
        }
        else if(in_array(3, $this->errors))
        {
            $this->throwError(3);
        }
        else if(in_array(17, $this->errors))
        {
            $this->throwError(17);

        }else if(in_array(7, $this->errors))
        {
            $this->throwError(7);

        }else if(in_array(9, $this->errors))
        {
            $this->throwError(9);

        }else if(in_array(11, $this->errors))
        {
            $this->throwError(11);
        }
        else if(in_array(13, $this->errors)){
            $cssDefaultPath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Css_Default.txt';
        
            $fileContentCss = file_get_contents($cssDefaultPath);
            if(!is_dir(substr($this->cssFilePath, 0, -strlen($this->controller . ".php")))){
                mkdir(substr($this->cssFilePath, 0, -strlen($this->controller . ".php")));
            }
            
            file_put_contents($this->cssFilePath, $fileContentCss);
            echo "Css was created!";
        }else{
            $this->throwError(12);
        }
    }

    public function create_js(){
        
        $this->addNewJs();
        header('Location:builder');
    }

    private function addNewJs(){
        if(in_array(1, $this->errors))
        {
            $this->throwError(1);
        }
        else if(in_array(3, $this->errors))
        {
            $this->throwError(3);
        }
        else if(in_array(17, $this->errors))
        {
            $this->throwError(17);

        }else if(in_array(7, $this->errors))
        {
            $this->throwError(7);

        }else if(in_array(9, $this->errors))
        {
            $this->throwError(9);

        }else if(in_array(11, $this->errors))
        {
            $this->throwError(11);
        }
        else if(in_array(15, $this->errors)){
            $jsDefaultPath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Js_Default.txt';
        
            $fileContentJs = file_get_contents($jsDefaultPath);
            if(!is_dir(substr($this->jsFilePath, 0, -strlen($this->controller . ".php")))){
                mkdir(substr($this->jsFilePath, 0, -strlen($this->controller . ".php")));
            }
            
            file_put_contents($this->jsFilePath, $fileContentJs);
            echo "Js was created!";
        }else{
            $this->throwError(14);
        }
    }

    public function create_all(){
        $this->addNewRoute();
        $this->addNewController();
        $this->addNewMethod();
        $this->addNewTemplate();
        $this->addNewPage();
        $this->addNewCss();
        $this->addNewJs();
        header('Location:builder');
    }

    public function delete_route(){
        
        $this->deleteRoute();
        header('Location:builder');
    }

    private function deleteRoute(){
        if(in_array(2, $this->errors))
        {
            $this->throwError(2);
        }
        else if(in_array(0, $this->errors)){
            $filePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Routes.txt';
            $routeToBeDeleted = $this->httpMethod . " " . $this->controller . "/" . $this->action . "/";
            $fileContent = file_get_contents($filePath);
            $fileContent = str_replace($routeToBeDeleted, "", $fileContent);
            file_put_contents($filePath, trim($fileContent));
            $this->check_route();
        }else{
            $this->throwError(1);
        }
    }

    public function delete_controller(){
        
        $this->deleteController();
        header('Location:builder');
    }

    private function deleteController(){
        $this->check_route();
        if(in_array(2, $this->errors)){
            $dirPath = substr($this->controllerFilePath, 0, -strlen($this->controller . ".php"));
            foreach(scandir($dirPath) as $file){
               
                if(($file === ".") || ($file === "..")){
                    continue;
                }
                unlink($dirPath . $file);
            }
            rmdir(substr($this->controllerFilePath, 0, -strlen($this->controller . ".php")));
            $this->check_route();
        }else{
            $this->throwError(3);
        }
    }
}