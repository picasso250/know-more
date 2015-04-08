var express = require('express');
var router = express.Router();

/* GET home page. */
router.get('/', function(req, res, next) {
  router.db.query('SELECT *from question limit 111', function(err, rows, fields) {
    if (err) throw err;
    res.render('index', { title: '我就是想知道更多', questions: rows});
  });
  // res.render('login', { title: '登录' });
  router.db.end();
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
      res.json({code: 1, message: 'no user'});
    } else {
      var user = rows[0];
      console.log(user.id, 'login');
      var sess = req.session
      sess.uid = user.id;
      res.json({code: 0, data: {url: '/'}});
    }
  });
  router.db.end();
});

router.get('/register', function(req, res, next) {
  res.render('register', { title: '注册' });
});
router.post('/register', function(req, res, next) {
  var params = [req.body.email];
  router.db.query('SELECT *from user where email=? limit 1', params, function(err, rows, fields) {
    if (err) throw err;
    if (rows.length > 0) {
      res.json({code: 1, message: 'user exists'});
      return;
    };
    // todo encrypt
    router.db.query('INSERT INTO user SET ?', req.body, function(err, result) {
      if (err) throw err;
      router.db.end();
      console.log(result.insertId);
      res.json({code: 0, message: 'register ok'});
    });
  })
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
    res.json({code: 0, data: {url: '/q/'+result.insertId}});
  });
  router.db.end();
});

router.get('/q/:qid', function(req, res, next) {
  var params = [req.params.qid];
  router.db.query('SELECT *from question where id=? limit 1', params, function(err, rows, fields) {
    if (err) throw err;
    if (rows.length === 0) {
      console.log('no user');
    } else {
      var question = rows[0];
      router.db.query('SELECT *from answer where qid=? limit 111', params, function(err, rows, fields) {
        router.db.end();
        if (err) throw err;
        var data = {
          title: question.title,
          question: question,
          answers: rows,
          show_answer: req.session.uid > 0 }
        res.render('question', data);
      })
    }
  });
});

router.post('/answer/:qid', function(req, res, next) {
  var params = [req.params.qid];
  router.db.query('SELECT *from question where id=? limit 1', params, function(err, rows, fields) {
    if (err) throw err;
    if (rows.length === 0) {
      console.log('no question');
    } else {
      console.log('find question',req.params.qid);
      params = req.body;
      params.qid = req.params.qid;
      params.uid = req.session.uid;
      console.log(params);
      router.db.query('INSERT INTO answer SET ?', params, function(err, result) {
        if (err) throw err;
        router.db.end();
        console.log(result.insertId);
        res.render('ask', { title: '回答成功' });
      });
    }
  });
});

module.exports = router;
