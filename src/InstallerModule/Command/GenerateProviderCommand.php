<?php

namespace InstallerModule\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of GenerateProviderCommnad
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class GenerateProviderCommand extends Command
{
    
    protected function configure()
    {
        $this
            ->setDescription('Generate a provider application')
            ->setName('app:generate:provider')
            ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
    }
    
}
