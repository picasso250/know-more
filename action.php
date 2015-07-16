<?php

namespace Action;

function index()
{
    \Occam\render();
}

function new_question()
{
    global $db;
    if (empty($_POST['question'])) {
        return \Occam\echo_json(1, 'no question');
    }
    $title = $_POST['question'];
    $detail = isset($_POST['detail']) ? $_POST['detail'] : '';
    $id = $db->insert('question', compact('title', 'detail'));
    return \Occam\echo_json(compact('id'));
}
