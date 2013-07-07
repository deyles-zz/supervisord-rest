<?php

namespace Liaison\rpc;

use Liaison\rpc\RemoteProcedureCallException;
use Liaison\rpc\RpcHttpRequest;
use Liaison\rpc\IXmlRpcAdaptor;
use Liaison\util\Configuration;

require_once(__DIR__ . '/RemoteProcedureCallException.php');
require_once(__DIR__ . '/RpcHttpRequest.php');
require_once(__DIR__ . '/../util/Configuration.php');

/**
 * Encapsulates all network I/O related logic. This class should be used to perform
 * all socket based calls against the supervisord RPC server.
 * 
 * @author deyles
 */
class RemoteProcedureCallNetworkDelegate {

    /**
     * The configuration for the network interface
     * 
     * @var Liaison\util\Configuration 
     */
    protected $configuration;

    /**
     * The network socket
     * 
     * @var file pointer 
     */
    protected $socket;

    /**
     * The XML RPC adaptor implementation
     * 
     * @var IXmlRpcAdaptor 
     */
    protected $adaptor;
    
    /**
     * Constructs a new RPC network interface
     * 
     * @param \Liaison\util\Configuration $configuration
     */
    public function __construct(Configuration $configuration, IXmlRpcAdaptor $adaptor) {
        $this->configuration = $configuration;
        $this->adaptor       = $adaptor;
    }

    /**
     * Validates the provided configuration, throwing an exception in the case
     * of missing required keys. Returns true on success.
     * 
     * @return boolean
     * @throws RemoteProcedureCallException
     */
    protected function validateConfiguration() {
        if (!$this->configuration->contains('host', 'rpc')) {
            throw new RemoteProcedureCallException('hostname has not been provided in configuration');
        }
        if (!$this->configuration->contains('port', 'rpc')) {
            throw new RemoteProcedureCallException('port has not been provided in configuration.');
        }
        return true;
    }

    /**
     * Establishes a socket connection to the specified RPC server
     * 
     * @return boolean
     * @throws RemoteProcedureCallException
     */
    public function connect() {
        $this->validateConfiguration();
        $this->socket = $this->adaptor->connect(
            $this->configuration->get('host', 'rpc'), 
            $this->configuration->get('port', 'rpc')
        );
        return true;
    }

    /**
     * Disconnects the network interface from the specified RPC server
     * 
     * @return boolean
     */
    public function disconnect() {
        if (!$this->socket) {
            return false;
        }
        $this->adaptor->disconnect($this->socket);
        return true;
    }

    /**
     * Performs the RPC call to the server
     * @param string $namespace
     * @param string $procedure
     * @param array $arguments
     * @return string
     * @throws RemoteProcedureCallException
     */
    public function call($namespace, $method, $arguments = array()) {
        if (!$this->socket) {
            $this->connect();
        }
        
        $xml = $this->adaptor->encodeRequest(
            $namespace, 
            $method, 
            $arguments, 
            array('encoding' => 'utf-8')
        );
        $this->adaptor->write($this->socket, $this->buildRPCRequest($xml));
        $response = $this->adaptor->decodeResponse($this->adaptor->read($this->socket));
        
        if (is_array($response) 
                && $this->adaptor->isFault($response)) {
            throw new RemoteProcedureCallException($response['faultString'], $response['faultCode']);
        }
        
        return $response;
    }

    /**
     * Constructs a string representation of a HTTP XML RPC request given the XML
     * payload as a string
     * @param string $xml
     * @return string
     */
    protected function buildRPCRequest($xml) {
        $request = new RpcHttpRequest();
        $request->setMethod('POST');
        $request->setUrl('/RPC2');
        $request->setBody($xml);
        $request->setAuthInformation(
            $this->configuration->get('username', 'rpc'),
            $this->configuration->get('password', 'rpc')
        );
        return $request->toString();
    }
        
}