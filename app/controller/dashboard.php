<?php

namespace controller;

class dashboard {
    
    use \traits\sendResponse;
    
    protected $container;

    function __construct(\Slim\Container $container) {
        $this->container = $container;
    }

    function __invoke($request, $response, $args) {
        
        // tidy db
        $this->container->db->delete("currentState", [
            "chtime[<]" => strtotime('yesterday midnight')
        ]);

        $active = $this->container->db->select('currentState', '*', ["mode" => 'login']);

        $raw = $this->container->db->select('audits', '*', [
            "ORDER" => ["chtime" => "DESC"],
	        "LIMIT" => 50
        ]);

        $response = $this->sendResponse($request, $response, "dashboard.phtml", [
            "raw"    => $raw,
            "active"   => $active
        ]);
        
        return $response;
    }
}