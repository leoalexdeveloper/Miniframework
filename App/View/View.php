<?php

namespace App\View;

class View{
    private array $uri;
    private array $data;

    public function __construct($uri, $data = []){
        $this->uri = $uri;
        $this->data = $data;
        $this->executeView();
    }

    private function executeView(){
        extract($this->uri);
        if(empty($this->data)){
            REQUIRE_ONCE(str_replace("\\", "/", dirname(__DIR__) . "/View/Templates/" . $controller . "/" . $controller . ".php"));
        }else{
            echo json_encode($this->data);   
        }
    }
}