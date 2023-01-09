<?php

class  Router{

    private array $handlers;

    private function addHandler(string $method,string $path,$handler){
        $params=[];
        $regex='~\{([^}]*)\}~';
        $dynamicUrl=preg_replace($regex,'(.+?)',$path);
        $this->handlers[$method.$path]=[
            'path'=>$dynamicUrl,
            'method'=>$method,
            'handler'=>$handler,
        ];
    }

    public function get(string $path,$handler){
        $this->addHandler('GET',$path,$handler);
    }

    public function post(string $path,$handler){
        $this->addHandler('POST',$path,$handler);
    }

    public function delete(string $path,$handler){
        $this->addHandler('DELETE',$path,$handler);
    }

    public function put(string $path,$handler){
        $this->addHandler('PUT',$path,$handler);
    }

    public function run(){
        $requestUri=parse_url($_SERVER['REQUEST_URI']);
        $requestUrl=$_SERVER['REQUEST_URI'];
        $requestPath=$requestUri['path'];
        $method=$_SERVER['REQUEST_METHOD'];
        $callback=null;
        $params=[];

        foreach ($this->handlers as $handler){
            if(preg_match("%^{$handler['path']}$%",$requestUrl,$matches)===1 && $method === $handler['method']){
                $callback=$handler['handler'];
                unset($matches[0]);
                $params=$matches;
                break;
            }
        }

        if(!$callback){
            if($_SERVER['REQUEST_METHOD']==='OPTIONS'){
//                for valid cors
                http_response_code(200);
                die('ERROR 404-URL NOT FOUND');
            } else{
                http_response_code(400);
                die('ERROR 404-URL NOT FOUND');
            }
        }

        call_user_func($callback,...$params);
    }
}