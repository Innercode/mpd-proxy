<?php

namespace App\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Controllers\WebsocketController;

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
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new WebsocketController()
                )
            ),
            getenv('wsPort')
        );
        $output->writeln('[WS] Server running on port ' . getenv('wsPort'));
        $server->run();
    }
}

