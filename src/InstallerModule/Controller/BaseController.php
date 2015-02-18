<?php

namespace InstallerModule\Controller;

use InstallerModule\Console\HtmlOutputFormatterDecorator;
use InstallerModule\Console\StringOutput;
use MVC\Controller\Controller;
use MVC\Tests\Provider\ConsoleSymfonyProvider;
use MVC\MVC;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Console\Input\StringInput;

/**
 * Description of BaseController
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
abstract class BaseController extends Controller
{
    
    /**
     *
     * @var Application
     */
    protected $application;
    
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
            ->initSymfonyConsoleProvider()
            ->setApplication($this->mvc->getCvpp('symfony.console'));
    }
    
    /**
     * Get the Symfony Console Application
     * 
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }
    
    /**
     * Get list commands to array strings
     * 
     * @return array
     */
    protected function getListCommands()
    {
        $commands = $this->application->all();
        $commandsArray = array();
        
        foreach ($commands as $command) {
            $commandsArray[] = array(
                'class'   => get_class($command),
                'name'    => $command->getName()
            );
        }
        
        return $commandsArray;
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
    
    /**
     * Init the Symfony Console Provider
     * 
     * @return BaseController
     */
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
        
        return $this;
    }
    
    /**
     * Set the Symfony Console Application
     * 
     * @param Application $application
     * @return BaseController
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
        
        return $this;
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
    
    protected function executeCommand($command)
    {
        // Cache can not be warmed up as classes can not be redefined during one request
        if(preg_match('/^cache:clear/', $command)) {
            $command .= ' --no-warmup';
        }
        
        $input  = new StringInput($command);
        $input->setInteractive(false);
        
        $output = new StringOutput();
        $formatter = $output->getFormatter();
        $formatter->setDecorated(true);
        $output->setFormatter(new HtmlOutputFormatterDecorator($formatter));
        
        // Some commands (i.e. doctrine:query:dql) dump things out instead of returning a value
        ob_start();
        $this->application->setAutoExit(false);
        $errorCode = $this->application->run($input, $output);
        // So, if the returned output is empty
        if (!$result = $output->getBuffer()) {
            $result = ob_get_contents(); // We replace it by the catched output
        }
        ob_end_clean();
        
        return array(
            'input'       => $command,
            'output'      => $result,
            'errorCode'  => $errorCode
        );
    }

    /**
     * Object to array
     * 
     * @param \stdClass $object
     * @return array
     */
    protected function objectToArray($object) 
    {
        if (is_object($object)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $object = get_object_vars($object);
        }

        if (is_array($object)) {
            /*
            * Return array converted to object
            * Using __FUNCTION__ (Magic constant)
            * for recursive call
            */
            return array_map(__METHOD__, $object);
        } else {
            // Return array
            return $object;
        }
    }
    
}
