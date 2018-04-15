<?php

namespace middleware\info;

class system {

    use \traits\sendResponse;
    
    protected $container;
    
    function __construct(\Slim\Container $container) {
        $this->container = $container;
    }

    public function __invoke($request, $response, $next) {
        
        $args = $request->getAttribute('routeInfo')[2];

        if ($request->isPut()) {

            $data = $request->getParsedBody();
            
            $name = filter_var(@$data['name'], FILTER_SANITIZE_STRING);
            $group = filter_var(@$data['group'], FILTER_SANITIZE_STRING);
            $wiki = filter_var(@$data['wiki'], FILTER_SANITIZE_STRING);

            if (
                !(is_string($name) &&
                strlen($name)) ||
                preg_match('/[^\x20-\x7f]/', $name)
            ) {
                $this->redirectWithMessage($response, "system", "error", ["Name is missing!", "Use only ASCII"], $args);
                return $response;
            } else if (
                !is_string($group) ||
                !$this->container->db->has("categories", ["id" => $group])
            ) {

                $this->redirectWithMessage($response, "system", "error", ["Group not found!", ""], ["id" => $args["id"]]);
                return $response;
            } else {
                return $next($request, $response);
            }
            
        } else if ($request->isDelete()) {
            if ($this->container->db->has("systems", ["AND" => ["id" => $args['id'], "approved" => true]])) {
                return $next($request, $response);
            } else {
                $this->redirectWithMessage($response, 'dashboard', "error", ["System not found!", ""]);
                return $response;
            }
        } else {
            return $next($request, $response);
        }

    }
}
