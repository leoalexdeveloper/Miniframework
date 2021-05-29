<?php
namespace App\Controller\Home;

use App\View\View;

/*
    Mesma classe
*/

class Home{
    private array $uri;
    public function __construct($uri){
        $this->uri = $uri;
        $this->view();
    }

    private function view(){
        (new View($this->uri));
    }
	


}