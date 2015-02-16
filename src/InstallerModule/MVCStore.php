<?php

namespace InstallerModule;

use MVC\MVC;
use Symfony\Component\Console\Application;

/**
 * Description of MCVStore
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class MVCStore
{
    
    static $vars = array();
    
    /**
     * Store a key and value
     * 
     * @param string $key  Array key
     * @param mixed $value Mixed value
     */
    static function store($key, $value = null)
    {
        self::$vars[$key] = $value;
    }
    
    /**
     * Retrieve stored key
     * 
     * @param string $key Array Key 
     * @return mixed
     */
    static function retrieve($key)
    {
        return self::$vars[$key];
    }
    
}
