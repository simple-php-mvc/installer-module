<?php

namespace InstallerModule\Generator;

use MVC\File\Explorer;

/**
 * Description of AbstractGenerator
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
abstract class AbstractGenerator
{
    
    static $instance;
    
    /**
     * Explorer files
     * 
     * @var Explorer
     */
    protected $explorer;
    
    public function __construct()
    {
        $this->explorer = new Explorer(dirname(__DIR__));
    }
    
    /**
     * Get instance singleton
     * 
     * @return AbstractGenerator
     */
    static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
}
