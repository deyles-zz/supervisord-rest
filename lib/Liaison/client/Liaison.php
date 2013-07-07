<?php

namespace Liaison\client;

use Liaison\rpc\ConcreteXmlRpcAdaptor;
use Liaison\rpc\RemoteProcedureCallNetworkDelegate;
use Liaison\rpc\RemoteProcedureCallException;
use Liaison\util\Configuration;

require_once(__DIR__ . '/../util/Configuration.php');
require_once(__DIR__ . '/../rpc/ConcreteXmlRpcAdaptor.php');
require_once(__DIR__ . '/../rpc/RemoteProcedureCallNetworkDelegate.php');
require_once(__DIR__ . '/../rpc/RemoteProcedureCallException.php');

/**
 * The primary facade providing an interface to the supervisord RPC API
 *
 * @author deyles
 */
class Liaison {

    /**
     * @var Liaison\util\Configuration 
     */
    protected $configuration;
    
    /**
     * @var Liaison\rpc\RemoteProcedureCallNetworkDelegate 
     */
    protected $delegate;
    
    /**
     * @var Liaison\rpc\ConcreteXmlRpcAdaptor 
     */
    protected $adaptor;
    
    /**
     * A list of all valid methods for the supervisord RPC server
     * 
     * @var array 
     */
    protected $methods = array();
    
    /**
     * Constructs a new Liaison client instance using the provided configuration
     * 
     * @param \Liaison\util\Configuration $configuration
     */
    public function __construct(Configuration $configuration) {
        $this->adaptor       = new ConcreteXmlRpcAdaptor();
        $this->configuration = $configuration;
        $this->delegate      = new RemoteProcedureCallNetworkDelegate(
            $this->configuration,
            $this->adaptor
        );
        $this->initialize();
    }
    
    /**
     * Sets up the client by retrieving a list of methods from the RPC server
     * and registering their names with the protected $methods array internal
     * to the class. This collection of names is subsequently used to validate
     * calls to the __call function to invoke remote methods.
     * 
     * @throws RemoteProcedureCallException
     */
    protected function initialize() {
        $methods = $this->delegate->call('system', 'listMethods');
        if (empty($methods)) {
            throw new RemoteProcedureCallException('unable to load method signatures from RPC server');
        }
        foreach ($methods as $method) {
            list ($namespace, $name) = explode('.', $method);
            $this->methods[$name] = $namespace;
        }
    }
    
    /**
     * Returns a boolean value indicating whether or not the provided method
     * name and parameters are valid
     * 
     * @param string $name
     * @return boolean
     * @throws RemoteProcedureCallException
     */
    public function validateMethod($name) {
        if (!isset($this->methods[$name])) {
            throw new RemoteProcedureCallException('unknown method: ' . $name);
        }
        return true;
    }
    
    public function __call($name, $arguments) {
        $this->validateMethod($name, $arguments);
        return $this->delegate->call($this->methods[$name], $name, $arguments);
    }
        
}