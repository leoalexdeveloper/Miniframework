<?php

namespace App\Controller\Builder;

class Builder{
    private $httpMethod;
    private $controller;
    private $action;
    private $methodVisibility;
    private $classComments;
    private $actionComments;
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
        '7' => "Set Pages Head first!",  
        '8' => "Css exists!",
        '9' => "Set Css first!",
        '10' => "Js exists!",
        '11' => "Set Js first!",
        '12' => "Action exists!",
        '13' => "Set Action first!",
        '14' => "Thie controller structure not exists yet!",
        '15' => "This route has partially initiated!",
        '26' => "The structure for this route was already done!",
        '27' => 'Missing Argunments: BuilderMethod / Http method / Controller / Action',
        '28' => 'Visibility not passed',
        '29' => 'Action not passed',
        '20' => 'Visibility must be public, protected or private',
        '21' => 'All folders must not exists. Delete a folder that exists and try again',
    ];

    public function __construct($uri){
        extract($uri);
        $uri = array_values(array_filter(explode("/", str_replace(SUB_FOLDER, "", $_SERVER['REQUEST_URI']))));

        $this->builderMethod = $uri[1] ? $uri[1] : $this->throwError(21);
        $this->httpMethod = isset($uri[2]) ? urldecode(strtoupper($uri[2])) : $this->throwError(21);
        $this->classComments = isset($uri[3]) ? $uri[3] = ucfirst(urldecode($uri[3])) : null;
        $this->controller = isset($uri[4]) ? $uri[4] = urldecode(ucfirst($uri[4])) : $this->throwError(21);
        $this->action = isset($uri[5]) ? $uri[5] = urldecode($uri[5]) : lcfirst($uri[4]);
        $this->methodVisibility = (isset($uri[6])) ? urldecode($uri[6]) : null;
        $this->actionComments = isset($uri[7]) ? $uri[7] = ucfirst(urldecode($uri[7])) : null;
        
        $this->routeFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Routes.txt';
        $this->controllerFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Controller/' . $this->controller . "/" . $this->controller . ".php";
        $this->templateFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/View/Templates/' . $this->controller . "/" . $this->controller . ".php";
        $this->pageBodyFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/View/Pages/' . $this->controller . "/Body.php";
        $this->pageHeadFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/View/Pages/' . $this->controller . "/Head.php";
        $this->pageFooterFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/View/Pages/' . $this->controller . "/Footer.php";
        $this->cssFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'Public/Css/' . $this->controller . "/" . $this->controller . ".css";
        $this->jsFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'Public/Js/' . $this->controller . "/" . $this->controller . ".js";
        $this->methodPath = '\\App\\Controller\\' . $this->controller . "\\" . $this->controller;

        
    }

    private function throwError($error){
        throw new \Exception($this->errorsIndex[$error]);
    }

    public function check_route(){
        
        $this->checkRoute();
        $this->checkFolderAndFilesStructure();
        $this->checkForClassMethod();
        $this->showStructureRelatory();
    }

    private function checkRoute(){
        $this->errors = [];
        $file = file_get_contents($this->routeFilePath);
        $pattern = $this->httpMethod . " " . lcfirst($this->controller) . "/" . $this->action . "/";
        $file = explode("\n", $file);
        foreach($file as $route){
            if(strcmp(trim($route), $pattern) === 0){
                $this->errors [] = 0;
            }
        }
        (in_array(0, $this->errors) === false) ? $this->errors [] = 1 : $this->errors = $this->errors;
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
            $this->errors [] = 6;
        }else{
            $this->errors [] = 7;
        }

        if(file_exists($this->pageFooterFilePath)){
            $this->errors [] = 6;
        }else{
            $this->errors [] = 7;
        }

        if(file_exists($this->cssFilePath)){
            $this->errors [] = 8;
        }else{
            $this->errors [] = 9;
        }

        if(file_exists($this->jsFilePath)){
            $this->errors [] = 10;
        }else{
            $this->errors [] = 11;
        }
    }

    private function checkForClassMethod(){
        
        if(in_array(2, $this->errors) && isset($this->action)){
            
            $reflection = new \ReflectionClass($this->methodPath);
            
            foreach($reflection->getMethods() as $method){
                
                if(strcmp(trim($method->name), $this->action) === 0){
                    $this->errors [] = 12;
                    return;
                }
            }
        }
        if(!in_array(12, $this->errors)){
            $this->errors [] = 13;
        } 
    }


    private function showStructureRelatory(){
        echo "<pre>";
        foreach($this->errors as $error){
           echo ($this->errorsIndex[$error]) . "<br>";
        }
        echo "</pre>";
    }

    private function addNewRoute(){
        
            $hasRoute = [];
            $filePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Routes.txt';
            $fileContent = file_get_contents($filePath);
            $controller = lcfirst($this->controller);
            $content = "['method' => '$this->httpMethod', 'route' => '$controller/$this->action/'],\n";
            $fileContentExploded = array_values(array_filter(explode("\n", $fileContent)));
            
            foreach($fileContentExploded as $route){
                
                if(strcmp(trim($route), trim($content)) === 0){
                    $hasRoute [] = $route;
                    break;
                }
            }
            
            if(empty($hasRoute)){
                file_put_contents($filePath, $fileContent . $content);
                $filePathSource = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Routes.txt';
                $filePathDestiny = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Router/Routes.php';
                $fileContent = file_get_contents($filePathSource);
                $content =  '<?php $this->routes = [' . "\n" . $fileContent . "];";
                file_put_contents($filePathDestiny, $content);
            }
    }

    private function addNewController(){
        
            $filePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Controller_Default.txt';
            $fileContent = file_get_contents($filePath);
            $fileContent = str_replace(["{{{controller}}}", "{{{comments}}}"], [$this->controller, $this->classComments], $fileContent);
            if(!is_dir(substr($this->controllerFilePath, 0, -strlen($this->controller . ".php")))){
                mkdir(substr($this->controllerFilePath, 0, -strlen($this->controller . ".php")));
            }
            file_put_contents($this->controllerFilePath, $fileContent);
    }

    public function create_action(){
        $this->checkRoute();
        $this->checkFolderAndFilesStructure();
        $this->checkForClassMethod();
        $this->addNewAction();
        $this->checkRoute();
        $this->checkFolderAndFilesStructure();
        $this->checkForClassMethod();
        $this->showStructureRelatory();
    }

    private function addNewAction(){
            
            $fileContent = trim(file_get_contents($this->controllerFilePath));
            $methodStructure = "\n\t/*\n\t\t" . $this->actionComments . "\n\t*/\n\t" . $this->methodVisibility . " function " . $this->action . "(){\n\n\t}\n}";
            $fileContent = substr($fileContent, 0, -1) . "\t" . $methodStructure;
            file_put_contents($this->controllerFilePath, $fileContent); 
    }

    private function addNewTemplate(){
        
            $templateDefaultPath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Template_Default.txt';
            $fileContent = file_get_contents($templateDefaultPath);
            if(!is_dir(substr($this->templateFilePath, 0, -strlen($this->controller . ".php")))){
                mkdir(substr($this->templateFilePath, 0, -strlen($this->controller . ".php")));
            }
            file_put_contents($this->templateFilePath, $fileContent);
    }

    private function addNewPage(){
        
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
    }

    private function addNewCss(){
        
            $cssDefaultPath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Css_Default.txt';
        
            $fileContentCss = file_get_contents($cssDefaultPath);
            if(!is_dir(substr($this->cssFilePath, 0, -strlen($this->controller . ".php")))){
                mkdir(substr($this->cssFilePath, 0, -strlen($this->controller . ".php")));
            }
            file_put_contents($this->cssFilePath, $fileContentCss);
    }


    private function addNewJs(){
        
            $jsDefaultPath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Js_Default.txt';
        
            $fileContentJs = file_get_contents($jsDefaultPath);
            if(!is_dir(substr($this->jsFilePath, 0, -strlen($this->controller . ".php")))){
                mkdir(substr($this->jsFilePath, 0, -strlen($this->controller . ".php")));
            }
            file_put_contents($this->jsFilePath, $fileContentJs);
    }

    public function create_all(){
        $this->checkRoute();
        $this->checkFolderAndFilesStructure();
        $this->checkForClassMethod();
        $this->createAll();
        $this->checkRoute();
        $this->checkFolderAndFilesStructure();
        $this->checkForClassMethod();
        $this->showStructureRelatory();
    }

    private function createAll(){
      
            $this->addNewRoute();
            $this->addNewController();
            $this->addNewAction();
            $this->addNewTemplate();
            $this->addNewPage();
            $this->addNewCss();
            $this->addNewJs();
    }

    public function delete_all(){
        $this->checkRoute();
        $this->checkFolderAndFilesStructure();
        $this->checkForClassMethod();
        $this->deleteAll();
        $this->checkRoute();
        $this->checkFolderAndFilesStructure();
        $this->checkForClassMethod();
        $this->showStructureRelatory();
    }

    private function deleteAll(){
        
        $filePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Documents/Routes.txt';
        $routeToBeDeleted = $this->httpMethod . " " . lcfirst($this->controller) . "/" . $this->action . "/";
        $fileContent = file_get_contents($filePath);
        $fileContent = str_replace($routeToBeDeleted, "", $fileContent);
        file_put_contents($filePath, trim($fileContent));
        
        $pathAndFileName = [
            $this->controllerFilePath => $this->controller . ".php" ,
            $this->templateFilePath => $this->controller . ".php" ,
            $this->pageHeadFilePath => "Head.php" ,
            $this->pageBodyFilePath => "Body.php" ,
            $this->pageFooterFilePath => "Footer.php" ,
            $this->cssFilePath => $this->controller . ".css",
            $this->jsFilePath => $this->controller . ".js",
        ];

       forEach($pathAndFileName as $key => $value){
            $dirPath = substr($key, 0, -strlen($value));
            if(is_dir($dirPath)){
                foreach(scandir($dirPath) as $file){
                    
                    if(($file === ".") || ($file === "..")){
                        continue;
                    }
                    if(file_exists($dirPath . $file)){
                        unlink($dirPath . $file);
                    }     
                }
            }   

            if(is_dir(substr($key, 0, -strlen($value)))){
                rmdir(substr($key, 0, -strlen($value)));
            }
        }
    }
}