## MPD Websocket proxy
This application maps the MPD socket to a websocket in order to communicate with MPD in HTML5.

### Installation
1. Clone this repository
2. Run `composer install` inside the directory
3. Copy the .env-example to .env and enter the necessary information
4. Run `php app.php mpdProxy`
5. You're now be able to connect to the websocket

### Introduction to the websocket
All the communication takes place in JSON. You must provide all the fields in the request in order to handle it.
In order to keep track of incoming messages you can use the callback variable. It will be copied to the response.

### Communication with MPD
The MPD commands can be found on http://www.musicpd.org/doc/protocol

#### Request
    {
        "type": "mpdCommand",
        "command": "currentsong",
        "callback": "currentsongCallback"
    }
    
#### Response
    {
        "value": "Artist: Llwellyn\nAlbumArtist: Llewellyn\nTitle: Hunter's Moon (The Wild Hunt)\nAlbum: Moonlore\nTrack: 9...",
        "callback": "currentsongCallback"
    }
