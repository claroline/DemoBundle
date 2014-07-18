<?php

namespace Claroline\DemoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * This class is more or less a copy paste of Claroline\CoreBundle\Library/Installation/Updater/WebUpdater.php
 *
 * @todo make Claroline\CoreBundle\Library/Installation/Updater/WebUpdater.php in plugins
 */
class ReplaceWebFolderCommand extends ContainerAwareCommand
{
    private $logger;
    private $files = [];
    private $webSrc;
    private $webProd;

    protected function configure()
    {
        $this->setName('claroline:demo:replace_web')
            ->setDescription('Update the web folder (replace app.php and add the reinstall.html.php file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rootDir = $this->getContainer()->getParameter('kernel.root_dir');
        $ds = DIRECTORY_SEPARATOR;
        $webSrc = "{$rootDir}{$ds}..{$ds}vendor{$ds}claroline{$ds}demo-bundle{$ds}Claroline{$ds}DemoBundle{$ds}Resources/web{$ds}";
        $webProd = "{$rootDir}{$ds}..{$ds}web{$ds}";
        $appFile = 'app.php';
        $reinstallFile = 'reinstall.html.php';
        unlink($webProd . $appFile);
        unlink($webProd . $reinstallFile);
        copy($webSrc . $appFile, $webProd . $appFile);
        copy($webSrc . $reinstallFile, $webProd . $reinstallFile);
    }
} 