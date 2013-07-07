<?php

namespace Liaison\rpc;

/**
 * A little (inner) class to help build HTTP requests for over the wire RPC
 * 
 * @author deyles
 */
class RpcHttpRequest {
    
    /**
     * The HTTP request method to use
     * 
     * @var string 
     */
    private $method = 'GET';
    
    /**
     * The URL to request
     * 
     * @var string 
     */
    private $url;
    
    /**
     * The request body (usually XML)
     * 
     * @var string 
     */
    private $body;

    /**
     * The Basic auth username
     * 
     * @var string 
     */    
    private $username;

    /**
     * The Basic auth password
     * 
     * @var string 
     */        
    private $password;
    
    /**
     * Constructs a new request container
     * @param string $method
     * @param string $url
     * @param string $body
     */
    public function __construct($method=null, $url=null, $body=null) {
        $this->method = $method;
        $this->url    = $url;
        $this->body   = $body;
    }
    
    public function setMethod($method) {
        $this->method = $method;
    }
    
    public function getMethod() {
        return $this->method;
    }
    
    public function setUrl($url) {
        $this->url = $url;
    }
    
    public function getUrl() {
        return $this->url;
    }
    
    public function setBody($body) {
        $this->body = $body;
    }

    public function getBody() {
        return $this->body;
    }

    public function setAuthInformation($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }    
    
    private function buildRequest() {
        return $this->method . ' ' . $this->url . " HTTP/1.1\r\n";
    }
    
    private function buildHeaders() {
        $headers = 'Content-Length: ' . strlen($this->body) . "\r\n";
        if (!empty($this->username) && !empty($this->password)) {
            $headers .= 'Authorization: Basic ' . 
                    base64_encode($this->username . ':' . $this->password) . "\r\n";
        }
        return $headers . "\r\n";
    }
    
    /**
     * Converts the request instance to a well formed HTTP request
     * @return string
     */
    public function toString() {
        $request  = $this->buildRequest();
        $request .= $this->buildHeaders();
        $request .= $this->body;
        return $request;
    }
    
}