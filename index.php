<?php

require 'xiaochi-db/autoload.php';
require 'occam/occam.php';
require 'action.php';

list($router, $args) = Occam\get_router();

$config = require 'config.php';
$dbc = $config['db'];
$db = new \Xiaochi\DB($dbc['dsn'], $dbc['username'], $dbc['password']);
$db->debug = $dbc['debug'];

session_start();

Occam\run($router, $args);
