<?php

namespace Liaison\util;

/**
 * An interface contract that configuration caches must implement
 * 
 * @author deyles
 */
interface IConfigurationCache {

    /**
     * Returns the cached configuration corresponding to the provided key
     * 
     * @param string $key
     */
    public function get($key);
    
    /**
     * Returns a boolean value indicating whether or not a cache configuration
     * exists corresponding to the provided key
     * 
     * @param string $key
     */
    public function contains($key);
    
    /**
     * Adds a configuration to the cache using the provided key
     * @param string $key
     * @param \Liaison\util\Configuration $configuration
     */
    public function set($key, Configuration $configuration);
    
}