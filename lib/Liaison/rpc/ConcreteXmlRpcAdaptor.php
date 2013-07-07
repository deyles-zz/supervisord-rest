<?php

namespace Liaison\rpc;

use Liaison\rpc\IXmlRpcAdaptor;
use Liaison\rpc\RemoteProcedureCallException;

require_once(__DIR__ . '/IXmlRpcAdaptor.php');
require_once(__DIR__ . '/RemoteProcedureCallException.php');

/**
 * A simple adaptor pattern implementation wrapping around the PHP 5.3 XML RPC
 * functions to make testing a bit easier. Also, I like abstraction.
 *
 * @author deyles
 */
class ConcreteXmlRpcAdaptor implements IXmlRpcAdaptor {

    /**
     * @see xmlrpc_encode_request
     * @param type $namespace
     * @param type $procedure
     * @param type $arguments
     * @param type $options
     */
    public function encodeRequest($namespace, $procedure, $arguments, $options) {
        return xmlrpc_encode_request(
            $namespace . '.' . $procedure, 
            $arguments, 
            $options
        );
    }

    /**
     * @see xmlrpc_decode
     * @param type $response
     */
    public function decodeResponse($response) {
        return xmlrpc_decode($response);
    }

    /**
     * @see xmlrpc_is_fault
     * @param type $response
     */
    public function isFault($response) {
        return xmlrpc_is_fault($response);
    }

    /**
     * Opens a connection to the provided host on the specified port. The return
     * value for this function is a file pointer.
     * @param string $hostname
     * @param string $port
     * @return file pointer
     * @throws Exception
     */
    public function connect($hostname, $port) {
        $errno = -1;
        $errstr = '';
        $url    = 'tcp://' .$hostname . ':' . $port;
        $socket = stream_socket_client(
            $url, 
            $errno, 
            $errstr
        );
        if (!$socket) {
            throw new RemoteProcedureCallException($errstr);
        }
        return $socket;
    }

    /**
     * Closes a connection
     * @param file pointer $socket
     */
    public function disconnect($socket) {
        @fclose($socket);
    }

    /**
     * Writes the provided message to the provided socket
     * @param file pointer $socket
     * @param string $message
     * @return boolean
     * @throws Exception
     */
    public function write($socket, $message) {
        $bytes = fputs($socket, $message, strlen($message));
        if (!$bytes) {
            throw new RemoteProcedureCallException('unable to write RPC request to socket');
        }
        return $bytes;
    }

    /**
     * Reads a message from the provided socket
     * @param file pointer $socket
     * @return string
     * @throws Exception
     */
    public function read($socket) {
        $buffer  = '';
        while (true) {
            $buffer .= @fgets($socket, 512);
            if (strcasecmp(substr($buffer, -18, 17), '</methodResponse>') === 0
                    || strcasecmp(substr($buffer, -9, 7), '</body>') === 0) {
                break;
            }
        }
        $message = @http_parse_message($buffer);
        if ($message->responseCode != 200) {
            throw new RemoteProcedureCallException($message->body);
        }
        return $message->body;
    }

}