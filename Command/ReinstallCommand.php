<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\DemoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class ReinstallCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('claroline:demo:refresh')
            ->setDescription('Refresh the demo');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ds = DIRECTORY_SEPARATOR;
        $rootDir = $this->getContainer()->getParameter('kernel.root_dir');
        $lastUpdateFile = $rootDir . $ds . 'config' . $ds . 'last_demo.txt';
        $refreshDemoFile = $rootDir . $ds . 'config' . $ds . '.refresh_demo';

        //set the last updated time
        $now = new \DateTime();
        file_put_contents($lastUpdateFile, $now->getTimeStamp());

        //set the refresh mode
        touch($refreshDemoFile);

        //remove all uploaded files
        $this->removeAllFiles();

        //reset the platform_options.yml file
        unlink($this->getContainer()->getParameter('claroline.param.platform_options_file'));

        //reset the claroline cache
        $this->getContainer()->get('claroline.manager.cache_manager')->refresh();

        //reinstall the platform
        $this->refresh($output);

        //remove the refresh mode
        unlink($refreshDemoFile);
    }

    private function refresh($output)
    {
        $dropDbCommand = $this->getApplication()->find('doctrine:database:drop');
        $createDbCommand = $this->getApplication()->find('doctrine:database:create');
        $installCommand = $this->getApplication()->find('claroline:install');
        $warmCacheCommand = $this->getApplication()->find('cache:warm');

        $dropDbCommand->run(new ArrayInput(array('--force' => true, 'command' => null)), $output);
        $createDbCommand->run(new ArrayInput(array('command' => null)), $output);
        //once we dropped the database, we lost the connection so we need to reinitialize it
        $connection = $this->getContainer()->get('doctrine.orm.entity_manager')->getConnection();
        $connection->close();
        $connection->connect();
        //install the platform
        $installCommand->run(new ArrayInput(array('command' => null)), $output);
    }

    //@todo clean the upload directory aswell
    private function removeAllFiles()
    {
        $this->removeDirectoryContent($this->getContainer()->getParameter('claroline.param.files_directory'));
        $this->removeDirectoryContent($this->getContainer()->getParameter('claroline.param.thumbnails_directory'));

    }

    private function removeDirectoryContent($directory)
    {
        $fs = new Filesystem();
        $targets = [];

        foreach (new \DirectoryIterator($directory) as $fileinfo) {
            if (!$fileinfo->isDot()) {
                if ($fileinfo->isDir()) {
                    $this->removeDirectoryContent($fileinfo->getPathname());
                }
                else if ('.gitkeep' !== $fileinfo->getFilename()) {
                    $targets[] = $fileinfo->getPathname();
                }
            }
        }

        $fs->remove($targets);
    }
} 