<?php

namespace BharatPHP;

class Services {

    private $services = array();
    private $loaded = array();

    public function get($service) {
        if (isset($this->services[$service])) {
            if (!isset($this->loaded[$service])) {
                $call = $this->services[$service]['call'];
                if (isset($this->services[$service]['params']) && !empty($this->services[$service]['params'])) {
                    $params = $this->services[$service]['params'];
                    if (strpos($call, '::')) {
                        $obj = call_user_func_array($call, $params);
                        $called = true;
                    } else if (strpos($call, '->')) {
                        $ary = explode('->', $call);
                        $class = $ary[0];
                        $method = $ary[1];
                        if (class_exists($class) && method_exists($class, $method)) {
                            $obj = call_user_func_array([new $class(), $method], $params);
                            $called = true;
                        }
                    } else if (class_exists($call)) {
                        $reflect = new \ReflectionClass($call);
                        $obj = $reflect->newInstanceArgs($params);
                        $called = true;
                    }
                } else {
                    if (strpos($call, '::')) {
                        $obj = call_user_func($call);
                        $called = true;
                    } else if (strpos($call, '->')) {
                        $ary = explode('->', $call);
                        $class = $ary[0];
                        $method = $ary[1];
                        if (class_exists($class) && method_exists($class, $method)) {
                            $obj = call_user_func([new $class(), $method]);
                            $called = true;
                        }
                    } else if (class_exists($call)) {
                        $obj = new $call();
                        $called = true;
                    }
                }

                if (!$called) {
                    throw new \Exception('Error: Unable to call service. The call parameter must be an object or something callable');
                } else {
                    $this->loaded[$service] = $obj;
                }
            }
            return $this->loaded[$service];
        }
    }

    public function set($name, $service) {

        $this->services[$name] = $service;

        return $this;
    }

}
