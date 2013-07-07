<?php

namespace Liaison\util;

require_once(__DIR__ . '/IConfigurationCache.php');
require_once(__DIR__ . '/Configuration.php');
require_once(__DIR__ . '/ConfigurationException.php');

/**
 * A factory for Configuration object instances
 *
 * @author deyles
 */
class ConfigurationFactory {

    /**
     * the configuration cache - used to hold previously instantiated cache
     * instances, rather than reloading each time.
     * 
     * @var IConfigurationCache 
     */
    protected $cache;
    
    /**
     * Constructs a new factory using the provided cache implementation
     * 
     * @param \Liaison\util\IConfigurationCache $cache
     */
    public function __construct(IConfigurationCache $cache) {
        $this->cache = $cache;
    }
    
    /**
     * Creates a new Configuration instance given the path of a well formed .ini
     * file. Namespaces within .ini files should be organized as sections:
     * 
     * [namespace1]
     * key=value
     * 
     * [namespace2]
     * key=value
     * 
     * @param string $path the path to the ini file to parse
     * @return \Liaison\util\Configuration the resulting Configuration instance
     * @throws ConfigurationException
     */
    public function factoryFromFile($path) {
        if (empty($path) || !file_exists($path)) {
            throw new ConfigurationException('configuration file does not exist');
        }
        if ($this->cache->contains($path)) {
            return $this->cache->get($path);
        }
        $configuration = new Configuration(parse_ini_file($path, true));
        $this->cache->set($path, $configuration);
        return $this->cache->get($path);
    }
        
}