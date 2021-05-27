<?php
namespace App\Controller\Home;

use App\View\View;
class Home{
    private array $uri;
    public function __construct($uri){
        $this->uri = $uri;
        $this->view();
    }

    public function home(){
        
    }

    private function view(){
        (new View($this->uri));
    }
}