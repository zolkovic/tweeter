<?php
namespace mf\utils;

class ClassLoader{

    private $prefix;

    public function __construct($src){
        $this->prefix = $src.'/';
    }

    private function loadClass($str){
        $class = $this->prefix.str_replace('\\',DIRECTORY_SEPARATOR,$str).'.php';
        if (file_exists($class)){
            require_once $class;
        }
    }

    public function register(){
        spl_autoload_register(array($this, 'loadClass'));
    }

}