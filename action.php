<?php

namespace Action;

function index()
{
    global $db;
    $list = $db->queryAll("select * from t");
    \Occam\render(compact('list'));
}
