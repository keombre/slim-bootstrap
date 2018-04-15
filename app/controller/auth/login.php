<?php

namespace controller\auth;

class login {

    use \traits\sendResponse;
    
    protected $container;

    function __construct(\Slim\Container $container) {
        $this->container = $container;
    }

    function __invoke($request, $response) {
        if ($request->isGet()) {

            $this->sendResponse($request, $response, "auth/login.phtml");

        } elseif ($request->isPost()) {

            $data = $request->getParsedBody();

            $name = filter_var(@$data['name'], FILTER_SANITIZE_STRING);
            $pass = filter_var(@$data['pass'], FILTER_SANITIZE_STRING);
            
            if ($this->container->auth->login($name, $pass)) {
                $this->container->logger->addInfo("Auth successfull for user " . $name);
                $response =  $response->withRedirect($this->container->router->pathFor('dashboard'), 301);
            } else {
                sleep(2);
                $this->container->logger->addInfo("Auth failed for user " . $name);

                $this->redirectWithMessage($response, 'login', "error", ["Login failed!", "Please try again"]);
            }
        }

        return $response;
    }
}