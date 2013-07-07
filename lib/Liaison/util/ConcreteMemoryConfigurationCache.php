<?php

namespace Liaison\util;

require_once(__DIR__ . '/IConfigurationCache.php');

/**
 * A memory bound cache for configurations
 *
 * @author deyles
 */
class ConcreteMemoryConfigurationCache implements IConfigurationCache {

    /**
     * @var array 
     */
    protected $cache = array();
    
    /**
     * Returns the cached configuration corresponding to the provided key
     * 
     * @param string $key
     */
    public function get($key) {
        if (!$this->contains($key)) {
            return null;
        }
        return $this->cache[$key];
    }
    
    /**
     * Returns a boolean value indicating whether or not a cache configuration
     * exists corresponding to the provided key
     * 
     * @param string $key
     */
    public function contains($key) {
        return isset($this->cache[$key]);
    }
    
    /**
     * Adds a configuration to the cache using the provided key
     * @param string $key
     * @param \Liaison\util\Configuration $configuration
     */
    public function set($key, Configuration $configuration) {
        $this->cache[$key] = $configuration;
        return true;
    }
    
}