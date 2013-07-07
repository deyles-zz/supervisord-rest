<?php

namespace Liaison\util;

/**
 * A simple configuration container. Configurations are organized as collections
 * of key/value pairs within optional namespaces.
 *
 * @author deyles
 */
class Configuration {
    
    /**
     * The default namespace for configuration contexts
     * @var string
     */
    const defaultNamespace = '__default__';
    
    /**
     * The configuration context for the config instance
     * @var array 
     */
    protected $context;
    
    /**
     * Constructs a new configuration instance, optionally taking an already
     * populated context in the form of an associative array.
     * @param array $context
     */
    public function __construct($context=array()) {
        $this->context = $context;
    }
    
    /**
     * Creates a namespace within the configuration context
     * @param string $namespace the name of the namespace to create
     * @return boolean
     */
    public function createNamespace($namespace) {
        if (!isset($this->context[$namespace])) {
            $this->context[$namespace] = array();
        }
        return true;
    }

    /**
     * Returns a boolean value indicating whether or not a given namespace 
     * exists within the configuration context
     * @param string $namespace
     * @return boolean
     */
    public function containsNamespace($namespace) {
        return isset($this->context[$namespace]);
    }
    
    /**
     * Sets a value corresponding to a given key within the configuration. If a
     * namespace is provided and does not already exist, it is created.
     * @param string $key
     * @param mixed $value
     * @param string $namespace (optional)
     */
    public function set($key, $value, $namespace = self::defaultNamespace) {
        if (!$this->containsNamespace($namespace)) {
            $this->createNamespace($namespace);
        }
        $this->context[$namespace][$key] = $value;
        return true;
    }
    
    /**
     * Returns the value corresponding to the provided key and optional namespace.
     * If the key doesn't exist NULL is returned.
     * @param string $key
     * @param string $namespace
     * @return mixed
     */
    public function get($key, $namespace = self::defaultNamespace) {
        if (!$this->contains($key, $namespace)) {
            return null;
        }
        return $this->context[$namespace][$key];
    }
    
    /**
     * Returns a boolean value indictating whether the provided key and optional
     * namespace exist within the configuration
     * @param string $key
     * @param string $namespace
     * @return boolean
     */
    public function contains($key, $namespace = self::defaultNamespace) {
        if ($this->containsNamespace($namespace)) {
            return isset($this->context[$namespace][$key]);
        }
        return false;
    }
    
}