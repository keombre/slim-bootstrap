<?php

namespace controller;

class config {
    
    use \traits\sendResponse;
    
    protected $container;

    function __construct(\Slim\Container $container) {
        $this->container = $container;
    }

    function __invoke($request, $response, $args) {
        
        if ($request->isPut()) {
            if ($args['key'] == "disableReg") {
                $this->container->config->set("new_reg", "false");
                $this->redirectWithMessage($response, 'dashboard', "status", ["Registration disabled.", ""]);
            } else if ($args['key'] == "enableReg") {
                $this->container->config->set("new_reg", "true");
                $this->redirectWithMessage($response, 'dashboard', "status", ["Registration enabled.", ""]);
            } else {
                $this->redirectWithMessage($response, 'dashboard', "error", ["Config key not found.", ""]);
            }
        }
        return $response;
    }
}