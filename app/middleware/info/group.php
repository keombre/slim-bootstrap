<?php

namespace middleware\info;

class group {

    use \traits\sendResponse;
    
    protected $container;
    
    function __construct(\Slim\Container $container) {
        $this->container = $container;
    }

    public function __invoke($request, $response, $next) {
        
        $args = $request->getAttribute('routeInfo')[2];

        if ($request->isPut()) {

            $data = $request->getParsedBody();
            
            $config = filter_var(@$data['config'], FILTER_SANITIZE_STRING);
            
            if ($args['id'] == "new") {

                $name = filter_var(@$data['name'], FILTER_SANITIZE_STRING);

                if (
                    !(is_string($name) &&
                    strlen($name)) ||
                    preg_match('/[^\x20-\x7f]/', $name)
                ) {
                    $this->redirectWithMessage($response, "group", "error", ["Name is missing!", "Use only ASCII"], ["id" => $args["id"]]);
                    return $response;
    
                } else if (
                    $this->container->db->has("categories", ["name" => $name])
                ) {
    
                    $this->redirectWithMessage($response, "group", "error", ["Group name alredy in use!", "Choose a different one."], ["id" => $args["id"]]);
                    return $response;
                } else {
                    return $next($request, $response);
                }
            } else {
                if (!$this->container->db->has("categories", ["id" => $args['id']])) {

                    $this->redirectWithMessage($response, "dashboard", "error", ["Group not found!", ""]);
                    return $response;
                } else {
                    return $next($request, $response);
                }
            }
            
        } else if ($request->isDelete()) {
            if ($args['id'] === 0) {
                $this->redirectWithMessage($response, "dashboard", "error", ["Cannot remove Default group", ""]);
                return $response;
            } else if (!$this->container->db->has("categories", ["id" => $args['id']])) {
                $this->redirectWithMessage($response, "dashboard", "error", ["Group not found!", ""]);
                return $response;
            } else {
                return $next($request, $response);
            }
        } else {
            return $next($request, $response);
        }

    }
}
