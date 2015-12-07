<?php 

namespace App\Controllers;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Controllers\MpdController;
use Symfony\Component\Console\Output\Output;

class WebsocketController implements MessageComponentInterface {
    protected $clients;
    protected $mpd;
    protected $mpdState;
   
    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->mpdConnect();
    }
    
    private function mpdConnect(Output $output){
        $this->mpd = new MpdController(env('mpdAddress'), env('mpdPort'));
        if($this->mpd->connect()){
            $output->writeln('[MPD] Connected to the mpd socket');
            $this->mpdState = true;
        } else {
            $output->writeln('[MPD] Could not connect to the mpd socket');
            $this->mpdState = false;
        }
    }
    
    public function onOpen(ConnectionInterface $conn, Output $output) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        $output->writeln('[WS] New connection!');
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        if ($this->mpd->checkConnection()) {
            $this->mpdState = true;
        } else {
            $this->mpdState = false;
            $this->mpdConnect();
        }
        
        if ($this->mpdState) {
            $msg = json_decode($msg);
            
            switch ($msg->type) {
                case 'echo':
                    $from->send(json_encode([
                        'value' => $msg->value,
                        'callback' => $msg->callback
                    ]));
                    break;
                    
                case 'mpdCommand':
                    if($this->mpdState){
                        $value = $this->mpd->writeCommand($msg->command);
                        $from->send(json_encode([
                            'value' => $value,
                            'callback' => $msg->callback
                        ]));
                    } else {
                        $from->send(json_encode([
                            'value' => '',
                            'callback' => $msg->callback
                        ]));
                    }
                    break;
            }
  
        }

    }

    public function onClose(ConnectionInterface $conn, Output $output) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        $output->writeln('[WS] A client disconnected');
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}