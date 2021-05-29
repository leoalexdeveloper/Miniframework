<?php

namespace App\Controller\Builder;

class Builder{
    private $arguments = [
        "className" => '',
        "builderMethod" => '',
        "builderAction" => '',
        "httpMethod" => '',
        "controller" => '',
        "action" => '',
        "classComments" => '',
        "actionComments" => '',
        "methodVisibility" => '',
    ];
    private $foldersPath = [
        "routeFile" => '',
        "controllerFile" => '',
        "templateFile" => '',
        "pageBodyFile" => '',
        "pageHeadFile" => '',
        "pageFooterFile" => '',
        "cssFile" => '',
        "jsFile" => '',
        "methodPath" => '',
    ];
    private $routeFilePath;
    private $controllerFilePath;
    private $templateFilePath;
    private $pageBodyFilePath;
    private $pageHeadFilePath;
    private $pageFooterFilePath;
    private $cssFilePath;
    private $jsFilePath;
    private $methodPath;

    private $builderRoutesFilePath;
    private $productionRoutesFilePath;

    public function __construct($uri){
        
        $uri = array_values(array_filter(explode("/", str_replace(SUB_FOLDER, "", $_SERVER['REQUEST_URI']))));
        $uriLength = count($uri);
        $argumentsKeys = array_keys($this->arguments);
        for($i = 0; $i < $uriLength; $i++){
            if($argumentsKeys[$i] === 'controller'){
                (isset($uri[$i])) ? $this->arguments[$argumentsKeys[$i]] = ucfirst($uri[$i]) : null;
            }elseif($argumentsKeys[$i] === 'methodVisibility'){
                (isset($uri[$i])) ? $this->arguments[$argumentsKeys[$i]] = ucfirst($uri[$i]) : "public";
            }else{
                (isset($uri[$i])) ? $this->arguments[$argumentsKeys[$i]] = $uri[$i] : null;
            }
        }

        $this->foldersPath = [
            "controllerFile" => $_SERVER["DOCUMENT_ROOT"] . SUB_FOLDER . "App/Controller/" . $this->arguments["controller"] . "/" . $this->arguments["controller"] . ".php",
            "templateFile" => $_SERVER["DOCUMENT_ROOT"] . SUB_FOLDER . "App/View/Templates/" . $this->arguments["controller"] . "/" . $this->arguments["controller"] . ".php",
            "pageBodyFile" => $_SERVER["DOCUMENT_ROOT"] . SUB_FOLDER . "App/View/Pages/" . $this->arguments["controller"] . "/Body.php",
            "pageHeadFile" => $_SERVER["DOCUMENT_ROOT"] . SUB_FOLDER . "App/View/Pages/" . $this->arguments["controller"] . "/Head.php",
            "pageFooterFile" => $_SERVER["DOCUMENT_ROOT"] . SUB_FOLDER . "App/View/Pages/" . $this->arguments["controller"] . "/Footer.php",
            "cssFile" => $_SERVER["DOCUMENT_ROOT"] . SUB_FOLDER . "Public/Css/" . $this->arguments["controller"] . "/" . $this->arguments["controller"] . ".css",
            "jsFile" => $_SERVER["DOCUMENT_ROOT"] . SUB_FOLDER . "Public/Js/" . $this->arguments["controller"] . "/" . $this->arguments["controller"] . ".js",
            "methodPath" => "\\App\\Controller\\" . $this->arguments["controller"] . "\\" . $this->arguments["controller"],
        ];

        $this->builderFoldersPath = [
            "controllerFile" => $_SERVER["DOCUMENT_ROOT"] . SUB_FOLDER . "App/Documents/Controller_Default.txt",
            "templateFile" => $_SERVER["DOCUMENT_ROOT"] . SUB_FOLDER . "App/Documents/Template_Default.txt",
            "pageBodyFile" => $_SERVER["DOCUMENT_ROOT"] . SUB_FOLDER . "App/Documents/Body_Default.txt",
            "pageHeadFile" => $_SERVER["DOCUMENT_ROOT"] . SUB_FOLDER . "App/Documents/Head_Default.txt",
            "pageFooterFile" => $_SERVER["DOCUMENT_ROOT"] . SUB_FOLDER . "App/Documents/Footer_Default.txt",
            "cssFile" => $_SERVER["DOCUMENT_ROOT"] . SUB_FOLDER . "App/Documents/Css_Default.txt",
            "jsFile" => $_SERVER["DOCUMENT_ROOT"] . SUB_FOLDER . "App/Documents/Js_Default.txt",
        ];

        $this->builderRoutesFilePath = $_SERVER["DOCUMENT_ROOT"] . SUB_FOLDER . "App/Documents/Routes.txt";
        $this->productionRoutesFilePath = $_SERVER['DOCUMENT_ROOT'] . SUB_FOLDER . 'App/Router/Routes.php';
    }

    private function returnFilePath($wantedPath){
        foreach($this->foldersPath as $key => $path){
            
            $remain = str_replace("File", "", $key);
            if($remain === $wantedPath){
                return $path;
            }
        }
        return;
    }

    private function returnBuilderFilePath($wantedPath){
        foreach($this->builderFoldersPath as $key => $path){
            if(strpos($key, $wantedPath) > -1){
                return $path;
            }
        }
        return;
    }

    private function validateFolderPaths(){
        $hasFolder = [];
        
        empty($this->checkRoute())  ? $hasFolder [] = "route " . $this->checkRoute() . " Not Exists" : $hasFolder [] = "route " . $this->checkRoute() . " Exists";

        foreach($this->foldersPath as $key => $path){
            $key = str_replace("File", "", $key);
            if(file_exists($this->returnFilePath($key))){
                $hasFolder [] = $key . " Exists at ". $path;
            }else{
                $hasFolder [] = $key . " Not Exists at ". $path;
            }
            if($key === 'controller' && file_exists($this->returnFilePath($key))){
                $hasFolder [] = $this->checkClassMethod($this->foldersPath['methodPath']);
            }
        }
        return $hasFolder;
    }


    public function exec(){
        $errors = [];
        switch($this->arguments["builderAction"]){
            case "check_route":
                if(isset($this->arguments["builderAction"]) && 
                    isset($this->arguments["httpMethod"]) && 
                    isset($this->arguments["controller"]) && 
                    isset($this->arguments["action"]))
                    {
                        $this->check_route();
                    }else{
                        $errors [] = "The url must have this structure: builderAction/httpMethod/controller/action";
                    }
            break;
            case "check_all_routes":
                if(isset($this->arguments["builderAction"]) && 
                    isset($this->arguments["httpMethod"]))
                    {
                        $this->check_all_routes();
                    }else{
                        $errors [] = "The url must have this structure: builderAction/httpMethod";
                    }
            break;
            case "check_class_method":
                if(isset($this->arguments["builderAction"]) && 
                    isset($this->arguments["httpMethod"]) && 
                    isset($this->arguments["controller"]) && 
                    isset($this->arguments["action"]) &&
                    isset($this->arguments["methodVisibility"]))
                    {
                        $this->check_class_method();
                    }else{
                        $errors [] = "The url must have this structure: builderAction/httpMethod/controller/action/methodVisibility";
                    }
            break;
            case "create_all":
                if(isset($this->arguments["builderAction"]) && 
                    isset($this->arguments["httpMethod"]) && 
                    isset($this->arguments["controller"]) && 
                    isset($this->arguments["action"]) &&
                    isset($this->arguments["methodVisibility"]))
                    {
                        $this->create_all();
                    }else{
                        $errors [] = "The url must have this structure: builderAction/httpMethod/controller/action/visibility/classComments/actionComments";
                    }
            break;
            case "delete_all":
                if(isset($this->arguments["builderAction"]) && 
                    isset($this->arguments["httpMethod"]) && 
                    isset($this->arguments["controller"]) && 
                    isset($this->arguments["action"]))
                    {
                        $this->delete_all();
                    }else{
                        $errors [] = "The url must have this structure: builderAction/httpMethod/controller/action";
                    }
            break;
            case "create_action":   
                if(isset($this->arguments["builderAction"]) && 
                    isset($this->arguments["httpMethod"]) && 
                    isset($this->arguments["controller"]) && 
                    isset($this->arguments["action"]) &&
                    isset($this->arguments["methodVisibility"]))
                    {
                        $this->create_action();
                    }else{
                        $errors [] = "The url must have this structure: builderAction/httpMethod/controller/action/visibility/-/actionComments";
                    }
            break;
        }
        $this->echoABrief($errors);
    }

    public function check_route(){
        
        $this->checkRoute();
    }

    private function checkRoute(){
        $method = strtoupper($this->arguments["httpMethod"]);
        $controller = lcfirst($this->arguments["controller"]);
        $action = $this->arguments["action"];
    
        $file = file_get_contents($this->builderRoutesFilePath);
        $pattern = "['method' => '$method', 'route' => '$controller/$action/'],";
        $file = explode("\n", $file);

        foreach($file as $route){
            
            if(trim($route) === trim($pattern)){
                return $route;
            }
        }
    }



    public function check_all_routes(){
        $this->checkAllRoutes();
    }

    private function checkAllRoutes(){
        $file = file_get_contents($this->builderRoutesFilePath);
        $file = explode(",\n", $file);
        
        foreach($file as $route){
            $this->echoABrief($route . "<br><br>");
        }
    }



    public function check_class_method(){
        $hasMethod = $this->checkClassMethod($this->foldersPath['methodPath']);
        $this->echoABrief($hasMethod);
    }

    private function checkClassMethod($path){
        
        $classPath = $path;
        $classReflection = new \ReflectionClass($classPath);
        
        foreach($classReflection->getMethods() as $action){
            if($this->arguments["action"] === trim($action->name)){
                return "action " . $action->name . " Exists";
            }
        }
        return;
    }



    private function addNewRoute(){
        
            $hasRoute = [];
            
            $fileContent = file_get_contents($this->builderRoutesFilePath);
            $httpMethod = strtoupper($this->arguments["httpMethod"]);
            $controller = lcfirst($this->arguments["controller"]);
            $action = $this->arguments["action"];
            
            if(strrpos($fileContent, "\n") < strlen($fileContent)-1){
                $content = "\n['method' => '{$httpMethod}', 'route' => '{$controller}/{$action}/'],\n";
            }else{
                $content = "['method' => '{$httpMethod}', 'route' => '{$controller}/{$action}/'],\n";
            }
            
            $fileContentExploded = array_values(array_filter(explode("\n", $fileContent)));
            
            foreach($fileContentExploded as $route){
                
                if(trim($route, "\n") === trim($content, "\n")){
                    $hasRoute [] = $route;
                    break;
                }
            }
            
            if(empty($hasRoute)){
                file_put_contents($this->builderRoutesFilePath, $fileContent . $content);
            }
            
            if(empty($hasRoute)){
                
                $productionRouterFilePath = $this->productionRoutesFilePath;
                $fileContent = file_get_contents($this->builderRoutesFilePath);
                $content = '<?php $this->routes = [' . "\n" . $fileContent . "];";
                file_put_contents($productionRouterFilePath, $content);
            }
    }

    private function addNewController(){
            
            $fileControllerPath = $this->returnFilePath('controller');
            $fileContent = file_get_contents($this->returnBuilderFilePath('controller'));
            $fileContent = str_replace(["{{{controller}}}", "{{{comments}}}"], [$this->arguments["controller"], $this->arguments["classComments"]], $fileContent);
            if(!is_dir(substr($this->returnFilePath('controller'), 0, -strlen($this->arguments["controller"] . ".php")))){
                mkdir(substr($this->returnFilePath('controller'), 0, -strlen($this->arguments["controller"] . ".php")));
            }
            
            file_put_contents($fileControllerPath, $fileContent);
    }

    private function create_action(){
        if(!$this->checkClassMethod($this->foldersPath['methodPath'])){
            $this->addNewAction();
        }else{
            $this->echoABrief($this->checkClassMethod($this->foldersPath['methodPath']));
        }
    }

    private function addNewAction(){
            
            $fileContent = file_get_contents($this->returnFilePath('controller'));
            $methodStructure = "\n\t/*\n\t\t" . $this->arguments["actionComments"] . "\n\t*/\n\t" . $this->arguments["methodVisibility"] . " function " . $this->arguments["action"] . "(){\n\n\t}\n}";
            $fileContent = substr($fileContent, 0, -1) . "\t" . $methodStructure;
            file_put_contents($this->returnFilePath('controller'), $fileContent); 
    }

    private function addNewTemplate(){
        
            $fileTemplatePath = $this->returnFilePath('template');
            $fileContent = file_get_contents($this->returnBuilderFilePath('template'));
            
            if(!is_dir(substr($fileTemplatePath, 0, -strlen($this->arguments["controller"] . ".php")))){
                mkdir(substr($fileTemplatePath, 0, -strlen($this->arguments["controller"] . ".php")));
            }
            file_put_contents($fileTemplatePath, $fileContent);
    }

    private function addNewPage(){
        
            $fileHeadPath = $this->returnFilePath('pageHead');
            $fileBodyPath = $this->returnFilePath('pageBody');
            $fileFoorterPath = $this->returnFilePath('pageFooter');
            $fileContentHead = file_get_contents($this->returnBuilderFilePath('pageHead'));
            $fileContentBody = file_get_contents($this->returnBuilderFilePath('pageBody'));
            $fileContentFooter = file_get_contents($this->returnBuilderFilePath('pageFooter'));
            if(!is_dir(substr($fileHeadPath, 0, -strlen("Head.php")))){
                mkdir(substr($fileHeadPath, 0, -strlen("Head.php")));
            }
            
            if(!is_dir(substr($fileBodyPath, 0, -strlen("Body.php")))){
                mkdir(substr($fileBodyPath, 0, -strlen("Body.php")));
            }

            if(!is_dir(substr($fileFoorterPath, 0, -strlen("Footer.php")))){
                mkdir(substr($fileFoorterPath, 0, -strlen("Footer.php")));
            }
            file_put_contents($fileHeadPath, $fileContentHead);
            file_put_contents($fileBodyPath, $fileContentBody);
            file_put_contents($fileFoorterPath, $fileContentFooter);
    }

    private function addNewCss(){
        
            $fileCssPath = $this->returnFilePath('css');
        
            $fileContentCss = file_get_contents($this->returnBuilderFilePath('css'));
            if(!is_dir(substr($fileCssPath, 0, -strlen($this->arguments["controller"] . ".php")))){
                mkdir(substr($fileCssPath, 0, -strlen($this->arguments["controller"] . ".php")));
            }
            file_put_contents($fileCssPath, $fileContentCss);
    }


    private function addNewJs(){
        
            $fileJsPath = $this->returnFilePath('js');
        
            $fileContentJs = file_get_contents($this->returnBuilderFilePath('js'));
            if(!is_dir(substr($fileJsPath, 0, -strlen($this->arguments["controller"] . ".php")))){
                mkdir(substr($fileJsPath, 0, -strlen($this->arguments["controller"] . ".php")));
            }
            file_put_contents($fileJsPath, $fileContentJs);
    }

    public function create_all(){
        $this->createAll();
    }

    private function createAll(){
        $foldersExixts = $this->validateFolderPaths();
        
        if(substr_count(implode("", $foldersExixts), "Not") === count($this->foldersPath) + 1){
            $this->addNewRoute();
            $this->addNewController();
            $this->addNewAction();
            $this->addNewTemplate();
            $this->addNewPage();
            $this->addNewCss();
            $this->addNewJs();
        }
        $foldersExixts = [];
        $foldersExixts = $this->validateFolderPaths();
        $this->echoABrief($foldersExixts);
    }

    public function delete_all(){
        $foldersExixts = $this->validateFolderPaths();
       
        if($this->checkRoute()){
            
            $this->deleteAll();
        }
        $foldersExixts = [];
        $foldersExixts = $this->validateFolderPaths();
        $this->echoABrief($foldersExixts);
    }

    private function deleteAll(){
        
        $fileRoutePath = $this->builderRoutesFilePath;
        $httpMethod = strtoupper($this->arguments['httpMethod']);
        $controller = lcfirst($this->arguments['controller']);
        $action = $this->arguments['action'];
        $routeToBeDeleted = "['method' => '{$httpMethod}', 'route' => '{$controller}/{$action}/'],";
        $fileContent = file_get_contents($fileRoutePath);
        $fileContent = str_replace($routeToBeDeleted, "", $fileContent);
        file_put_contents($fileRoutePath, trim($fileContent));
        
        $pathAndFileName = [
            $this->returnFilePath('controller') => $this->arguments["controller"] . ".php" ,
            $this->returnFilePath('template') => $this->arguments["controller"] . ".php" ,
            $this->returnFilePath('pageHead') => "Head.php" ,
            $this->returnFilePath('pageBody') => "Body.php" ,
            $this->returnFilePath('pageFooter') => "Footer.php" ,
            $this->returnFilePath('css') => $this->arguments["controller"] . ".css",
            $this->returnFilePath('js') => $this->arguments["controller"] . ".js",
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

    private function echoABrief($brief){
        if(gettype($brief) === "string"){

            echo ucfirst($brief);

        }else if(gettype($brief) === "array"){

            foreach($brief as $topic){
                echo "<pre>";
                echo ucfirst($topic);
                echo "</pre>";
            }

        }
    }
}