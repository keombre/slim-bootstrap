<?php

namespace controller;

final class api {
    
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

        if ($this->container->db->has('currentState', ["uname" => $uname, "station" => $station])) {
            $this->container->db->update("currentState", [
                    "uname" => $uname,
                    "station" => $station,
                    "mode" => $mode,
                    "chtime" => time()
                ], [
                    "AND" => [
                        "uname" => $uname,
                        "station" => $station
                    ]
                ]
            );
        } else {
            $this->container->db->insert("currentState", [
                "uname" => $uname,
                "station" => $station,
                "mode" => $mode,
                "chtime" => time()
            ]);
        }

        return $response->withJson(["ok"]);

    }

}
