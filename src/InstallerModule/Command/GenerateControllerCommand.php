<?php

namespace InstallerModule\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of GenerateControllerCommand
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class GenerateControllerCommand extends Command
{
    
     protected function configure()
    {
        $this
            ->setDescription('Generate a controller application')
            ->setName('app:generate:controller')
            ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
    }
}
