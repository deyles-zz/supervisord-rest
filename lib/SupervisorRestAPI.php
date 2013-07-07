<?php

require_once('Slim/Slim.php');
require_once('Liaison/client/Liaison.php');

use Slim\Slim;
use Liaison\client\Liaison;

class SupervisorRestAPI extends Slim {

    protected $liaison;

    public function __construct($configuration, $userSettings = array()) {
        parent::__construct($userSettings);
        $this->liaison = new Liaison($configuration);
    }

    public function getApi() {
        return $this->liaison;
    }
    
}

class SupervisorRestAPIDecorator {

    public function decorate(SupervisorRestAPI $app) {
        $app->contentType('application/json');
        
        /**
         * Status & Control
         */
        $app->get('/supervisor/v1/status/api_version', function () use ($app) {
            echo(json_encode($app->getApi()->getAPIVersion()));
        });
        
        $app->get('/supervisor/v1/status/version', function () use ($app) {
            echo(json_encode($app->getApi()->getSupervisorVersion()));
        });

        $app->get('/supervisor/v1/status/identifier', function () use ($app) {
            echo(json_encode($app->getApi()->getIdentification()));
        });
        
        $app->get('/supervisor/v1/status/state', function () use ($app) {
            echo(json_encode($app->getApi()->getState()));
        });

        $app->get('/supervisor/v1/status/pid', function () use ($app) {
            echo(json_encode($app->getApi()->getPID()));
        });

        $app->get('/supervisor/v1/status/log', function () use ($app) {
            echo(json_encode($app->getApi()->readLog(
                $app->request()->get('offset'), 
                $app->request()->get('length')                  
            )));
        });
        
        $app->post('/supervisor/v1/status/control', function() use ($app) {
            $name = $app->request()->post('command');
            if ($name == 'shutdown') {
                echo json_encode(
                    $app->getApi()->shutdown()
                );
            } else if ($name == 'restart') {
                echo json_encode(
                    $app->getApi()->restart()
                );
            } else if ($name == 'clearLog') {
                echo json_encode(
                    $app->getApi()->clearLog()
                );
            }
        });        
        
        /**
         * Process control
         */
        $app->get('/supervisor/v1/control/processes/:name', function($name) use ($app) {
            echo json_encode($app->getApi()->getProcessInfo($name));
        });

        $app->get('/supervisor/v1/control/processes', function() use ($app) {
            echo json_encode($app->getApi()->getAllProcessInfo());
        });

        $app->post('/supervisor/v1/control/processes', function() use ($app) {
            $name = $app->request()->post('name');
            if ($name == 'all') {
                echo json_encode(
                    $app->getApi()->startAllProcesses(
                        $app->request()->post('wait')
                    )
                );                
            } else {
                echo json_encode(
                    $app->getApi()->startProcess(
                        $name,
                        $app->request()->post('wait')
                    )
                );
            }
        });        
        
        $app->post('/supervisor/v1/control/groups/:name', function($name) use ($app) {
            $command = $app->request()->post('command');
            if ($command == 'start') {
                echo json_encode(
                    $app->getApi()->startProcessGroup($name, $app->request()->post('wait'))
                );
            } else if ($command == 'stop') {
                echo json_encode(
                    $app->getApi()->stopProcessGroup($name, $app->request()->post('wait'))
                );                
            }
        });        
        
        $app->delete('/supervisor/v1/control/groups/:name', function($name) use ($app) {
            echo json_encode(
                $app->getApi()->removeProcessGroup($name)
            );
        });
      
        $app->post('/supervisor/v1/control/groups', function() use ($app) {
            echo json_encode(
                $app->getApi()->addProcessGroup($app->request()->post('name'))
            );
        });        
        
        /**
         * System methods
         */
        $app->get('/supervisor/v1/system/methods/list', function () use ($app) {
            echo(json_encode($app->getApi()->listMethods()));
        });

        $app->get('/supervisor/v1/system/methods/:name/help', function($name) use ($app) {
            echo json_encode($app->getApi()->methodHelp($name));
        });

        $app->get('/supervisor/v1/system/methods/:name/signature', function($name) use ($app) {
            echo json_encode($app->getApi()->methodSignature($name));
        });

        /**
         * Logging methods
         */
        $app->get('/supervisor/v1/logging/processes/:name/stdout/read', function($name) use ($app) {
            echo json_encode(
                $app->getApi()->readProcessStdoutLog(
                    $name, 
                    $app->request()->get('offset'), 
                    $app->request()->get('length')
                )
            );
        });

        $app->get('/supervisor/v1/logging/processes/:name/stderr/read', function($name) use ($app) {
            echo json_encode(
                $app->getApi()->readProcessStderrLog(
                    $name, 
                    $app->request()->get('offset'), 
                    $app->request()->get('length')
                )
            );
        });

        $app->get('/supervisor/v1/logging/processes/:name/stdout/tail', function($name) use ($app) {
            echo json_encode(
                $app->getApi()->tailProcessStdoutLog(
                    $name, 
                    $app->request()->get('offset'), 
                    $app->request()->get('length')
                )
            );
        });

        $app->get('/supervisor/v1/logging/processes/:name/stderr/tail', function($name) use ($app) {
            echo json_encode(
                $app->getApi()->tailProcessStderrLog(
                    $name, 
                    $app->request()->get('offset'), 
                    $app->request()->get('length')
                )
            );
        });

        $app->post('/supervisor/v1/logging/clear', function() use ($app) {
            $name = $app->request()->post('name');
            if ($name == 'all') {
                echo json_encode(
                    $app->getApi()->clearAllProcessLogs()
                );
            } else {
                echo json_encode(
                    $app->getApi()->clearProcessLogs($name)
                );
            }
        });
    }

}