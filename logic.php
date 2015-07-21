<?php

function user_id()
{
    $key = '_suid';
    if (func_num_args()) {
        $_SESSION[$key] = func_get_arg(0);
    } else {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : 0;
    }
}


function ensure_user_email($email)
{
    global $db;
    $user = $db->get_user_by_email($_GET['email']);
    if (empty($user)) {
        $id = $db->insert('user', ['email' => $email, 'show_name' => '无名']);
        $user = $db->get_user_by_id($id);
    }
    return $user;
}
