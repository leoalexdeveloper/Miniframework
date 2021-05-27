<?php
namespace App\Controller\Great;

use App\View\View;

/*
    Builder
*/

class Great{
    private array $uri;
    public function __construct($uri){
        $this->uri = $uri;
        $this->view();
    }

    private function view(){
        (new View($this->uri));
    }

    public function great(){
        
    }
	
	/*
		Builder method
	*/
	private function newGreat777(){

	}
}