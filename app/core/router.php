<?php 

class Router
{

    public static $halts = true;

    public static $routes = array();

    public static $methods = array();

    public static $callbacks = array();

    public static $patterns = array(
        ':any' => '[^/]+',
        ':num' => '[0-9]+',
        ':all' => '.*'
    );

    public static $error_callback;

    /**
     * Defines a route w/ callback and method
     */
    public static function __callstatic($method, $params) 
    {
      
        $uri = dirname($_SERVER['PHP_SELF']).'/'.$params[0];
        $callback = $params[1];

        array_push(self::$routes, $uri);
        array_push(self::$methods, strtoupper($method));
        array_push(self::$callbacks, $callback);
    }

    /**
     * Defines callback if route is not found
    */
    public static function error($callback)
    {
        self::$error_callback = $callback;
    }

    public static function haltOnMatch($flag = true)
    {
        self::$halts = $flag;
    }

    /**
     * Runs the callback for the given request
     */
    public static function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];  

        $searches = array_keys(static::$patterns);
        $replaces = array_values(static::$patterns);

        self::$routes = str_replace('//','/',self::$routes);   

        $found_route = false;

        // check if route is defined without regex
        if (in_array($uri, self::$routes)) {

            $route_pos = array_keys(self::$routes, $uri);
            foreach ($route_pos as $route) {

                if (self::$methods[$route] == $method || self::$methods[$route] == 'ANY') {
                    $found_route = true;

                    //if route is not an object 
                    if(!is_object(self::$callbacks[$route])){
                        
                        $parts = explode('@',self::$callbacks[$route]);
                        $file = strtolower('app/controllers/'.$parts[0].'.php'); 
                        
                        //try to load and instantiate model     
                        if(file_exists($file)){
                            require $file;
                        }

                        //grab all parts based on a / separator 
                        $parts = explode('/',self::$callbacks[$route]);

                        //collect the last index of the array
                        $last = end($parts);

                        //grab the controller name and method call
                        $segments = explode('@',$last);                         

                        //instanitate controller
                        $controller = new $segments[0]();

                        //call method
                        $controller->$segments[1](); 

                        if (self::$halts) return;
                        
                    } else { 

                        //call closure
                        call_user_func(self::$callbacks[$route]);

                        if (self::$halts) return;
                    }
                }
            }
        } else {

            // check if defined with regex
            $pos = 0;
            foreach (self::$routes as $route) {

                $route = str_replace('//','/',$route);

                if (strpos($route, ':') !== false) {
                    $route = str_replace($searches, $replaces, $route);
                }

                if (preg_match('#^' . $route . '$#', $uri, $matched)) {

                    if (self::$methods[$pos] == $method || self::$methods[$pos] == 'ANY') {
                        $found_route = true; 

                        array_shift($matched); //remove $matched[0] as [1] is the first parameter.

                        if(!is_object(self::$callbacks[$pos])){

                            $parts = explode('@',self::$callbacks[$pos]);
                            $file = strtolower('app/controllers/'.$parts[0].'.php'); 
                            
                            //try to load and instantiate model     
                            if(file_exists($file)){
                                require $file;
                            }

                            //grab all parts based on a / separator 
                            $parts = explode('/',self::$callbacks[$pos]); 

                            //collect the last index of the array
                            $last = end($parts);

                            //grab the controller name and method call
                            $segments = explode('@',$last); 

                            //instanitate controller
                            $controller = new $segments[0]();

                            $params = count($matched);

                            //call method and pass any extra parameters to the method
                            switch ($params) {
                                case '0':
                                    $controller->$segments[1]();
                                    break;
                                case '1':
                                    $controller->$segments[1]($matched[0]);
                                    break;
                                case '2':
                                    $controller->$segments[1]($matched[0],$matched[1]);
                                    break;
                                case '3':
                                    $controller->$segments[1]($matched[0],$matched[1],$matched[2]);
                                    break;
                                case '4':
                                    $controller->$segments[1]($matched[0],$matched[1],$matched[2],$matched[3]);
                                    break;
                                case '5':
                                    $controller->$segments[1]($matched[0],$matched[1],$matched[2],$matched[3],$matched[4]);
                                    break;
                                case '6':
                                    $controller->$segments[1]($matched[0],$matched[1],$matched[2],$matched[3],$matched[4],$matched[5]);
                                    break;
                                case '7':
                                    $controller->$segments[1]($matched[0],$matched[1],$matched[2],$matched[3],$matched[4],$matched[5],$matched[6]);
                                    break;
                                case '8':
                                    $controller->$segments[1]($matched[0],$matched[1],$matched[2],$matched[3],$matched[4],$matched[5],$matched[6],$matched[7]);
                                    break;
                                case '9':
                                    $controller->$segments[1]($matched[0],$matched[1],$matched[2],$matched[3],$matched[4],$matched[5],$matched[6],$matched[7],$matched[8]);
                                    break;
                                case '10':
                                    $controller->$segments[1]($matched[0],$matched[1],$matched[2],$matched[3],$matched[4],$matched[5],$matched[6],$matched[7],$matched[8],$matched[9]);
                                    break;
                            }

                            if (self::$halts) return;
                            
                        } else {
                            
                            call_user_func_array(self::$callbacks[$pos], $matched);
                       
                            if (self::$halts) return;
                        }
                        
                    }
                }
            $pos++;
            }
        }
 

        // run the error callback if the route was not found
        if ($found_route == false) {
            if (!self::$error_callback) {
                self::$error_callback = function() {
                    header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
                    echo '404';
                };
            } 

            $parts = explode('@',self::$error_callback);
            $file = strtolower('app/controllers/'.$parts[0].'.php'); 
            
            //try to load and instantiate model     
            if(file_exists($file)){
                require $file;
            }

            if(!is_object(self::$error_callback)){

                //grab all parts based on a / separator 
                $parts = explode('/',self::$error_callback); 

                //collect the last index of the array
                $last = end($parts);

                //grab the controller name and method call
                $segments = explode('@',$last);

                //instanitate controller
                $controller = new $segments[0]('No routes found.');

                //call method
                $controller->$segments[1]();

                if (self::$halts) return;

            } else {
               call_user_func(self::$error_callback); 

               if (self::$halts) return;
            }
            
        }
    }
}
