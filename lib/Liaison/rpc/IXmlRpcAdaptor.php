<?php

namespace Liaison\rpc;

/**
 * @author deyles
 */
interface IXmlRpcAdaptor {

    /**
     * @see xmlrpc_encode_request
     * @param type $namespace
     * @param type $procedure
     * @param type $arguments
     * @param type $options
     */
    public function encodeRequest($namespace, $procedure, $arguments, $options);
    
    /**
     * @see xmlrpc_decode
     * @param type $response
     */
    public function decodeResponse($response);
    
    /**
     * @see xmlrpc_is_fault
     * @param type $response
     */
    public function isFault($response);
    
    /**
     * Opens a connection to the provided host on the specified port. The return
     * value for this function is a file pointer.
     * @param string $hostname
     * @param string $port
     * @return file pointer
     * @throws Exception
     */
    public function connect($hostname, $port);
    
    /**
     * Closes a connection
     * @param file pointer $socket
     */
    public function disconnect($socket);
    
    /**
     * Writes the provided message to the provided socket
     * @param file pointer $socket
     * @param string $message
     * @return boolean
     * @throws Exception
     */
    public function write($socket, $message);
    
    /**
     * Reads a message from the provided socket
     * @param file pointer $socket
     * @return string
     * @throws Exception
     */
    public function read($socket);
    
}