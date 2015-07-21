<?php

require '../xiaochi-db/autoload.php';
require '../occam/occam.php';
require '../logic.php';
require '../action.php';

list($router, $args) = \Occam\get_router();

$config = require '../config.php';
$dbc = $config['db'];
$db = new Xiaochi\DB($dbc['dsn'], $dbc['username'], $dbc['password']);

session_start();
$user = null;
if (filter_input(INPUT_GET, 'login_state', FILTER_VALIDATE_BOOLEAN) && isset($_GET['token']) && isset($_GET['email'])) {
    if ($_GET['token'] === sha1("$_GET[email]-{$config['account']['secret']}")) {
        // check email
        $user = ensure_user_email($_GET['email']);
        user_id($user['id']);
    }
} elseif (user_id()) {
    $user = $db->get_user_by_id(user_id());
}

\Occam\run($router, $args);
