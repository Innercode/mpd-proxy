<?php
if (!$loader = include __DIR__.'/vendor/autoload.php') {
    die('You must set up the project dependencies.');
}

//Init dotenv
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

//Init console
$app = new \Symfony\Component\Console\Application();
$app->add(new App\Commands\MpdProxyCommand());
$app->run();