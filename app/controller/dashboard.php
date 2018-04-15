<?php

namespace controller;

class dashboard {
    
    use \traits\sendResponse;
    
    protected $container;

    function __construct(\Slim\Container $container) {
        $this->container = $container;
    }

    function __invoke($request, $response, $args) {
        $pending    = $this->container->db->select("systems", "*", ["approved" => false]);
        $approved   = $this->container->db->select("systems", "*", ["approved" => true]);
        $categories = [];

        foreach ($this->container->db->select("categories", "*") as $cat) {
            $categories[$cat['id']] = [
                "name" => $cat['name'],
                "config" => $cat['config']
            ];
        }

        $response = $this->sendResponse($request, $response, "dashboard.phtml", [
            "pending"    => $pending,
            "approved"   => $approved,
            "categories" => $categories
        ]);
        return $response;
    }
}