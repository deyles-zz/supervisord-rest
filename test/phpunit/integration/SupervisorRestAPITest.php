<?php

require_once(__DIR__ . '/../../../lib/SupervisorRestAPI.php');
require_once(__DIR__ . '/../../../lib/Liaison/util/Configuration.php');
require_once(__DIR__ . '/../../../lib/Slim/Slim.php');

use Slim\Slim;
use Slim\Environment;
use Liaison\util\Configuration;

Slim::registerAutoloader();

class SupervisorRestAPITest extends PHPUnit_Framework_TestCase {

    protected $app;
    protected $request;
    protected $response;
    
    protected function encode($params) {
        $pairs = array();
        foreach ($params as $key => $value) {
            $pairs[] = "$key=$value";
        }
        return implode('&', $pairs);
    }
    
    public function request($method, $path, $params = array(), $options = array()) {
        
        $env = array(
            'REQUEST_METHOD' => $method,
            'PATH_INFO'      => $path,
            'SERVER_NAME'    => 'test.me',
        );
        if (!empty($params)) {
            if ($method == 'GET') {
                $env['QUERY_STRING'] = $this->encode($params);
            } else {
                $env['slim.input']   = $this->encode($params);
            }
        }        
        Environment::mock(array_merge($env, $options));
        
        $configuration = new Configuration(
            array(
                'rpc' => array(
                    'host' => '166.78.248.82',
                    'port' => '9001',
                    'username' => 'test',
                    'password' => 'test'
                )
            )
        );

        $this->app = new SupervisorRestAPI($configuration);
        $composer = new SupervisorRestAPIDecorator();
        $composer->decorate($this->app);

        ob_start();        
        
        $this->app->run();
        $this->request  = $this->app->request();
        $this->response = $this->app->response();
        
        return ob_get_clean();
    }

    public function get($path, $params = array(), $options = array()) {
        $this->request('GET', $path, array(), $options);
    }    

    public function post($path, $params = array(), $options = array()) {
        $this->request('POST', $path, $params, $options);
    }        

    public function delete($path, $params = array(), $options = array()) {
        $this->request('DELETE', $path, $params, $options);
    }    
    
    public function testApi() {
        // TODO: write this
    }

}