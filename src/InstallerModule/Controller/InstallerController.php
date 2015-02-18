<?php

namespace InstallerModule\Controller;

use MVC\MVC;

class InstallerController extends BaseController
{

    function index(MVC $mvc)
    {
        # Configure commands
        $this->configure($mvc);
        
        $commands = $this->getListCommands();
        
        return $mvc->getCvpp('twig')->render('Installer/index.twig', array(
            'commands' => $commands
        ));
    }

    function execute(MVC $mvc, $command)
    {
        # Configure commands
        $this->configure($mvc);
        
        $result = parent::executeCommand($command);
        
        return $mvc->getCvpp('twig')->render('Installer/execute.twig', array(
            'command' => $result['command'],
            'result'  => $result['output'],
            'error'   => $result['errorCode']
        ));
    }
}
