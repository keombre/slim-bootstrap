<?php

final class routes {

    function __construct(\Slim\App $app) {
        
        $app->group($app->getContainer()->get('settings')['path'], function() {
            
            /**
             * has to be authed
             */
            $this->group('', function() {
                
                $this->get('/', \controller\dashboard::class)
                    ->setName('dashboard');
            })->add(\middleware\auth::class);
            
            $this->map(['GET', 'POST'], '/login', \controller\auth\login::class)
                ->add(\middleware\login::class)
                ->setName('login');
            
            $this->get('/api/{mode}/{uname}[/{station}]', \controller\api::class)
                ->add(\middleware\token::class);
            
        });

    }

}
