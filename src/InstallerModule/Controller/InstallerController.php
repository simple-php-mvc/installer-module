<?php

namespace InstallerModule\Controller;

use MVC\MVC;

class InstallerController extends BaseController
{

    function index(MVC $mvc)
    {
        # Configure commands
        $this->configure($mvc);
        
        
        
        return $mvc->getCvpp('twig')->render('Installer/index.twig');
    }

    private function execute($command)
    {
        $app = new Application($this->get('kernel'));
        $app->setAutoExit(false);

        $input = new StringInput($command);
        $output = new BufferedOutput();

        $error = $app->run($input, $output);

        if ($error != 0)
            $msg = "Error: $error";
        else
            $msg = $output->getBuffer();
        return $msg;
    }
}
