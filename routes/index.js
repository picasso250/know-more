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
  res.render('login', { title: '登录' });
});
router.post('/login', function(req, res, next) {
  var params = [req.body.email, req.body.password];
  router.db.query('SELECT *from user where email=? and password=? limit 1', params, function(err, rows, fields) {
    if (err) throw err;
    if (rows.length === 0) {
      console.log('no user');
    } else {
      var user = rows[0];
      console.log(user.id, 'login');
      var sess = req.session
      sess.uid = user.id;
      res.render('login', { title: '登录成功' });
    }
  });
  // res.render('login', { title: '登录' });
  router.db.end();
});

router.get('/register', function(req, res, next) {
  res.render('register', { title: '注册' });
});
router.post('/register', function(req, res, next) {

  // todo encrypt
  router.db.query('INSERT INTO user SET ?', req.body, function(err, result) {
    if (err) throw err;

    console.log(result.insertId);
  });

  router.db.end();
  res.render('register', { title: '注册成功' });
});

router.get('/admin', function(req, res, next) {
  res.render('index', { title: '这里是后台' });
});

router.get('/ask', function(req, res, next) {
  res.render('ask', { title: '提问' });
});
router.post('/ask', function(req, res, next) {
  router.db.query('INSERT INTO question SET ?', req.body, function(err, result) {
    if (err) throw err;
    console.log(result.insertId);
    res.render('ask', { title: '提问成功' });
  });
  router.db.end();
});

module.exports = router;
