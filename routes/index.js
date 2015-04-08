var express = require('express');
var router = express.Router();

/* GET home page. */
router.get('/', function(req, res, next) {
  res.render('index', { title: '我就是想知道更多' });
});

router.get('/new/question', function(req, res, next) {
  res.render('index', { title: '提一个问题' });
});
router.get('/login', function(req, res, next) {
  res.render('index', { title: '登录' });
});

router.get('/register', function(req, res, next) {
  res.render('register', { title: '注册' });
});
router.post('/register', function(req, res, next) {
  console.log(req.body)
  var mysql      = require('mysql');
  var connection = mysql.createConnection({
    host     : 'localhost',
    user     : 'root',
    password : 'root',
    database : 'know_more'
  });

  // connection.query('SELECT 1 + 1 AS solution', function(err, rows, fields) {
  //   if (err) throw err;
  //   console.log('The solution is: ', rows[0].solution);
  // });
  // var data = {email: req.params.}
  connection.query('INSERT INTO user SET ?', req.body, function(err, result) {
    if (err) throw err;

    console.log(result.insertId);
  });

  connection.end();
  res.render('register', { title: '注册成功' });
});

router.get('/admin', function(req, res, next) {
  res.render('index', { title: '这里是后台' });
});

module.exports = router;
