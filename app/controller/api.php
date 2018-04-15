<?php

namespace controller;

final class api_login {
    
    protected $container;
    protected $token;

    function __construct(\Slim\Container $container) {
        $this->container = $container;
    }

    function __invoke($request, $response) {

        $station    = $request->getAttribute("station");
        $uname      = $request->getAttribute("uname");
        $mode      = $request->getAttribute("mode");

        $this->container->db->insert("audits", [
            "uname" => $uname,
            "station" => $station,
            "mode" => $mode,
            "chtime" => time()
        ]);

        return $response->withJson(["ok"]);

    }

}
