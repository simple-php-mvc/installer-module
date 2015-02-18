<?php

namespace InstallerModule\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of GeneratorModuleCommand
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class GeneratorModuleCommand extends Command
{
    
    protected function configure()
    {
        $this
            ->setDescription('Generate a module application.')
            ->setName('app:generate:module')
            ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
    }
    
}
