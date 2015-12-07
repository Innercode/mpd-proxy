<?php

namespace App\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class MpdProxyCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('mpdProxy')
            ->setDescription('Run the MPD proxy');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('MpdProxy');
    }
}

