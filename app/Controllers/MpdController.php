<?php

namespace App\Controllers;

class MpdController {
    
    protected $address;
    protected $port;
    protected $lock;
    protected $client;
    
    public function __construct($address = null, $port = null)
    {
        $this->address = $address;
        $this->port = $port;
    }
    
    public function setAddress($address)
    {
        $this->server = $address;
    }
    
    public function setPort($port)
    {
        $this->port = $port;
    }
    
    public function connect()
    {
        $this->client = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (socket_connect($this->client, $this->address, $this->port)) {
            if (strpos(socket_read($this->client, 1024), 'MPD') !== false) {
                return true;
            }
        }
        return false;
    }
    
    public function disconnect()
    {
        return socket_shutdown($this->client);
    }
    
    public function checkConnection()
    {
        if (socket_write($this->client, 'ping' . PHP_EOL)) {
            if (trim(socket_read($this->client, 1024)) == 'OK') {
               return true; 
            }
        }
        return false;
    }
    
    public function writeCommand($command = null, $arguments = null)
    {
        if (socket_write($this->client, $command . PHP_EOL)) {
            return trim(socket_read($this->client, 1048576));
        }
        return false;
    }
}