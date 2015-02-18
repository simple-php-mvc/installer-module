<?php

namespace InstallerModule\Command;

use InstallerModule\MVCStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of AssetInstallCommand
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class AssetsInstallCommand extends Command
{
    
    public function __construct()
    {
        parent::__construct('app:assets:install');
    }
    
    protected function configure()
    {
        $this
            ->addArgument('folder', InputArgument::OPTIONAL, 'Folder for assets', 'web')
            ->addOption('symlinks', null, InputOption::VALUE_NONE, 'Make symbolic links to public folder modules.')
            ->setDescription('Install modules assets to the folder');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        # MVC object
        $mvc = MVCStore::retrieve('mvc');
        if ($input->getArgument('folder') == 'web') {
            $input->setArgument('folder', dirname($mvc->getAppDir()) . '/web');
        }
        
        $folderArg = rtrim($input->getArgument('folder'), '/');
        if (!is_dir($folderArg)) {
            throw new \InvalidArgumentException(sprintf('The target directory "%s" does not exist.', $input->getArgument('folder')));
        }
        
        $modulesPath = $folderArg . '/modules/';
        @mkdir($modulesPath);
        
        if ($input->getOption('symlinks')) {
            $output->writeln('Trying to install assets as <comment>symbolic links</comment>.');
        } else {
            $output->writeln('Installing assets as <comment>hard copies</comment>.');
        }
        
        foreach ($mvc->getModules() as $module) {
            if (is_dir($originDir = $module->getPath() . '/Resources/public')) {
                $targetDir = $modulesPath . preg_replace('/module$/', '', strtolower($module->getName()));
                
                $output->writeln(sprintf('Installing assets for <comment>%s</comment> into <comment>%s</comment>', $module->getNamespace(), $targetDir));
                
                if (!$this->recursiveRemoveDir($targetDir)) {
                    $output->writeln(sprintf('Could\'t been removed the dir "%s".', $targetDir));
                }
                
                if ($input->getOption('symlinks')) {
                    #link($originDir, $targetDir);
                    @symlink($originDir, $targetDir);
                } else {
                    $this->resourceCopy($originDir, $targetDir);
                }
            }
        }
    }
    
    /**
     * Copy directory to destiny directory
     * 
     * @param string $sourceDir
     * @param string $destinyDir
     */
    protected function resourceCopy($sourceDir, $destinyDir)
    {
        $dir = opendir($sourceDir);
        @mkdir($destinyDir);
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($sourceDir . '/' . $file)) {
                    $this->resourceCopy($sourceDir . '/' . $file, $destinyDir . '/' . $file);
                } else {
                    copy($sourceDir . '/' . $file, $destinyDir . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
    
    /**
     * Recursive Remove Dir
     * 
     * @param string $dir
     * @return boolean
     */
    function recursiveRemoveDir($dir)
    {
        if (is_link($dir)) {
            @unlink($dir);
        } else if (is_dir($dir)) {
            $files = array_diff(@scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? $this->recursiveRemoveDir("$dir/$file") : @unlink("$dir/$file");
            }
        }
        return @rmdir($dir);
    }

}
