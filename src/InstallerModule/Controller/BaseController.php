<?php

namespace InstallerModule\Controller;

use MVC\Controller\Controller;
use MVC\Tests\Provider\ConsoleSymfonyProvider;
use MVC\MVC;

/**
 * Description of BaseController
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
abstract class BaseController extends Controller
{
    
    /**
     * MVC object
     * 
     * @var MVC
     */
    protected $mvc;
    
    protected function configure(MVC $mvc)
    {
        $this
            ->setMvc($mvc)
            ->initSymfonyConsoleProvider();
    }
    
    /**
     * Get MVC object
     * 
     * @return MVC
     */
    public function getMvc()
    {
        return $this->mvc;
    }
    
    protected function initSymfonyConsoleProvider()
    {
        if (!$this->mvc->hasCvpp('symfony.console')) {
            $this->mvc->registerProvider(new ConsoleSymfonyProvider(array(
                'modules'  => $this->mvc->setModules(),
                'commands' => array(
                    new ListCommand()
                )
            )));
        }
    }
    
    /**
     * Set the MVC object
     * 
     * @param MVC $mvc MVC object
     * @return BaseController
     */
    public function setMvc(MVC $mvc)
    {
        $this->mvc = $mvc;
        
        return $this;
    }

}
