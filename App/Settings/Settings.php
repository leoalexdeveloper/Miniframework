<?php

if(strpos(dirname(__DIR__), ":\\") > -1){
    
    $subfolder = array_values(array_filter(explode("/", $_SERVER['REQUEST_URI']))); 
    define("SUB_FOLDER", "/" . $subfolder[0] . "/");
}else{
    define("SUB_FOLDER", "/");
}


/* Builder Settings */
define("BUILDER_LOG", "ACTIVE");