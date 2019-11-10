<?php 

namespace mf\router;
use mf\utils\HttpRequest as HttpRequest;

class Router extends AbstractRouter {

    public function __construct() {
        parent::__construct();
    }

    public function addRoute($alias, $url, $controller, $method, $access_level = \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE){
        self::$routes[$url] = array($controller, $method, $access_level);
        self::$aliases[$alias] = $url;
    }

    public function setDefaultRoute($url){
        self::$aliases['default'] = $url;
    }

    public function run(){
        $alias = str_replace('/', '', $this->http_req->path_info);
        if (array_key_exists($alias, self::$aliases)){
            $route = self::$aliases[$alias];
        }else{
            $route = self::$aliases['default'];
        }
        if (array_key_exists($route, self::$routes )) {
            $auth = new \mf\auth\Authentification();
            if($auth->checkAccessRight(self::$routes[$route][2])){
                $controller = self::$routes[$route][0];
                $method = self::$routes[$route][1];
                $ctrl = new $controller();
                $ctrl->$method();
            }else{
                $route = self::$aliases['default'];
                $controller = self::$routes[$route][0];
                $method = self::$routes[$route][1];
                $ctrl = new $controller();
                $ctrl->$method();
            }
        }
    }

    static public function executeRoute($alias) {
        if(array_key_exists($alias, self::$aliases)) {
            $chemin = self::$aliases[$alias];
            $controller = self::$routes[$chemin][0];
            $method = self::$routes[$chemin][1];
            $ctrl = new $controller();
            $ctrl->$method();
        }
    }

    static public function urlFor($alias, $data = NULL){
        $httpReq = new HttpRequest();//$this->http_req;
        $param_html = '';
        if(!empty($data)){
            $param_html = '?';
            $only_one = true;
            foreach ($data as $key => $value){
                if ($only_one == false){
                    $param_html .= '&';
                }
                $param_html .= $key.'='.$value;
                $only_one = false;
            }
        }
        return $httpReq->script_name.self::$aliases[$alias].$param_html;
    }

    
}