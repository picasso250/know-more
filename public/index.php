<?php

require '../xiaochi-db/autoload.php';
require '../occam/occam.php';
require '../logic.php';
require '../action.php';

list($router, $args) = \Occam\get_router();

$config = require '../config.php';
$dbc = $config['db'];
$db = new Xiaochi\DB($dbc['dsn'], $dbc['username'], $dbc['password']);

$user = ['show_name' => 'wxc', 'detail' => 'xxx'];

\Occam\run($router, $args);
