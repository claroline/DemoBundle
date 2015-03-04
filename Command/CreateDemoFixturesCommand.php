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

use Claroline\DemoBundle\DataFixtures\LoadDemoFixture;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDemoFixturesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('claroline:demo:load')
            ->setDescription('Load the demo fixtures');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Loading demo fixtures...');
        $fixture = new LoadDemoFixture();
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $referenceRepo = new ReferenceRepository($em);
        $fixture->setReferenceRepository($referenceRepo);
        $fixture->setContainer($this->getContainer());
        $verbosityLevelMap = array(
            LogLevel::NOTICE => OutputInterface::VERBOSITY_NORMAL,
            LogLevel::INFO   => OutputInterface::VERBOSITY_NORMAL,
            LogLevel::DEBUG  => OutputInterface::VERBOSITY_NORMAL
        );
        $consoleLogger = new ConsoleLogger($output, $verbosityLevelMap);
        $fixture->setLogger($consoleLogger);
        $fixture->load($em);
        $output->writeln('Done');
    }
}
