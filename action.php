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

    $answer_by_me = false;
    $answers = $db->all_answer_by_qid($id);
    foreach ($answers as &$answer) {
        if ($answer['uid'] == user_id()) {
            $answer_by_me = true;
        }
        $answer['user'] = $db->get_user_by_id($answer['uid']);
        $answer['up'] = $db->all_vote_by_aid_and_vote($answer['id'], 1);
        foreach ($answer['up'] as $vote) {
            $answer['up_users'][] = $db->get_user_by_id($vote['uid']);
        }
    }

    \Occam\render(compact('question', 'answers', 'answer_by_me'));
}

function vote_post($aid)
{
    global $db;
    $data = compact('aid');
    $data['uid'] = user_id();
    $data['vote'] = $_POST['vote'];
    $id = $db->insert('vote', $data);
    return \Occam\echo_json(compact('id'));
}

function add_answer($qid)
{
    global $db;
    if (empty($_POST['content'])) {
        return \Occam\echo_json(1, 'empty');
    }
    if ($db->get_answer_by_qid_and_uid($qid, user_id())) {
        return \Occam\echo_json(1, 'u have answered');
    }
    $content = $_POST['content'];
    $data = compact('content', 'qid');
    $data['uid'] = user_id();
    $data['edit_time'] = $db::timestamp();
    $id = $db->insert('answer', $data);
    return \Occam\echo_json(compact('id'));
}
