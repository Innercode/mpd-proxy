<?php

namespace App\Controllers;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Dotenv\Dotenv;

class WebsocketController implements MessageComponentInterface {
    protected $clients;
    protected $mpd;
    protected $mpdState;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->mpdConnect();

    }

    private function mpdConnect(){
        $this->mpd = new MpdController(getenv('mpdAddress'), getenv('mpdPort'));
        if($this->mpd->connect()){
            echo "[MPD] Connected to the mpd socket\n";
            $this->mpdState = true;
        } else {
            echo "[MPD] Could not connect to the mpd socket\n";
            $this->mpdState = false;
        }
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
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

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}