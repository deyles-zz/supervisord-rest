<?php

namespace Liaison\client;

use Liaison\util\Configuration;
use Liaison\util\ConfigurationFactory;
use Liaison\util\ConcreteMemoryConfigurationCache;
use Liaison\client\Liaison;

require_once(__DIR__ . '/../util/Configuration.php');
require_once(__DIR__ . '/../util/ConfigurationException.php');
require_once(__DIR__ . '/../util/ConfigurationFactory.php');
require_once(__DIR__ . '/../util/ConcreteMemoryConfigurationCache.php');
require_once(__DIR__ . '/Liaison.php');

/**
 * A factory class to abstract the complexity of constructing Liaison client
 * class instances.
 * 
 * @author deyles
 */
class LiaisonFactory {

    /**
     * Constructs a new Liaison instance using the provided configuration
     * 
     * @param \Liaison\util\Configuration $configuration
     * @return \Liaison\client\Liaison
     */
    public function factoryClient(Configuration $configuration) {
        return new Liaison($configuration);
    }
    
    /**
     * Constructs a new Liaison instance using the provided ini file as
     * configuration
     * 
     * @see factoryClient
     * @param string $path the path to the ini file on disk
     * @return \Liaison\client\Liaison
     */
    public function factoryClientUsingIni($path) {
        $cache   = new ConcreteMemoryConfigurationCache();
        $factory = new ConfigurationFactory($cache);
        $configuration = $factory->factoryFromFile($path);
        return $this->factoryClient($configuration);
    }
    
}