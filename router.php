<?php

class Router
{
    protected $request = null;
    protected $routes = [];
    protected $postRoutes = [];

    public function __construct(IRequest $request)
    {
        $this->request = $request;
    }

    public function get($path, $closure)
    {
        $this->routes[$path] = $closure;
    }

    public function post($path, $closure)
    {
        $this->postRoutes[$path] = $closure;
    }

    public function resolve()
    {
        $path = $_SERVER['PATH_INFO'] ?? '/';
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($method === 'get') {
            $closureOrView = $this->routes[$path] ?? false;
        } else {
            $closureOrView = $this->postRoutes[$path] ?? false;
        }
        if ($closureOrView) {
            if (is_callable($closureOrView)) {
                echo call_user_func($closureOrView, $this->request, $this);
            } else {
                echo $this->renderView($closureOrView);
            }
        } else {
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
            echo "Not Found";
            exit;
        }
    }

    public function renderView($view, $params = [])
    {
        $content = $this->renderOnlyView($view, $params); // <h1>Home page</h1>
        ob_start();
        include_once __DIR__ . '/view/_layout.php';
        return ob_get_clean();
    }

    public function renderOnlyView($view, $params = [])
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        /**
         * 111<h1>Home page</h1>
         */
        ob_start();
        include_once __DIR__ . '/view/'.$view.'.php';
        return ob_get_clean(); /**  */
    }

    public function __destruct()
    {
        $this->resolve();
    }
}