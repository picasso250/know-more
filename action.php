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

function question($id)
{
    global $db;
    $question = $db->get_question_by_id($id);

    $answers = $db->all_answer_by_qid($id);
    foreach ($answers as &$answer) {
        
    }

    \Occam\render(compact('question', 'answers'));
}

function add_answer($qid)
{
    global $db;
    if (empty($_POST['content'])) {
        return \Occam\echo_json(1, 'empty');
    }
    $content = $_POST['content'];
    $id = $db->insert('answer', compact('content', 'qid'));
    return \Occam\echo_json(compact('id'));
}
