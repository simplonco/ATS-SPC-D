var express = require('express');
var router = express.Router();
var passport = require('passport');
var LocalStrategy = require('passport-local').Strategy;
var nodemailer = require('nodemailer');

var User = require('../models/user');

// Register
router.get('/register', function(req, res, next) {
  res.render('register');
});

// Login
router.get('/login', function(req, res, next) {
  res.render('login');
});

// Add Date
router.get('/add', function(req, res, next) {
  res.render('add');
});

// Send Email
// Create a SMTP transporter object
var transporter = nodemailer.createTransport({
  pool: true,
  service: 'Gmail',
  auth: {
    user: 'hamoui.antoine@gmail.com',
    pass: 'nthk1980##'
  },
  logger: true, // log to console
  debug: true // include SMTP traffic in the logs
}, {
  // default message fields

  // sender info
  from: 'Sender Name <sender@example.com>',
  headers: {
    'X-Laziness-level': 1000 // just an example header, no need to use this
  }
});

console.log('SMTP Configured');

// Mock message queue. In reality you would be fetching messages from some external queue
var messages = [{
  to: '"Chadi" <thamoui@hotmail.com>',
  subject: 'Nodemailer is unicode friendly ✔', //
  text: 'Hello to myself!',
  html: '<p><b>Hello</b> world!</p>'
}];

// send mail only if there are free connection slots available
transporter.on('idle', function () {
  // if transporter is idling, then fetch next message from the queue and send it
  while (transporter.isIdle() && messages.length) {
    console.log('Sending Mail');
    transporter.sendMail(messages.shift(), function (error, info) {
      if (error) {
        console.log('Error occurred');
        console.log(error.message);
        return;
      }
      console.log('Message sent successfully!');
      console.log('Server responded with "%s"', info.response);
    });
  }
});


// Register User
router.post('/register', function(req, res) {
  var name = req.body.name;
  var email = req.body.email;
  var username = req.body.username;
  var password = req.body.password;
  var password2 = req.body.password2;

  //Validation
  req.checkBody('name', 'Name is required').notEmpty();
  req.checkBody('email', 'Email is required').notEmpty();
  req.checkBody('email', 'Email is not valid').isEmail();
  req.checkBody('username', 'Username is required').notEmpty();
  req.checkBody('password', 'Password is required').notEmpty();
  req.checkBody('password2', 'Passwords do not match').equals(req.body.password);


  var errors = req.validationErrors();

  if(errors){
    res.render('register',{
      errors:errors
    });
  }else{
    var newUser = new User({
      name: name,
      email: email,
      username: username,
      password: password
    });

    User.createUser(newUser, function(err, user){
      if(err) throw err;
      console.log(user);
    });

    req.flash('success_msg', 'You are registered and can now login');

    res.redirect('/users/login');
  }
});

passport.use(new LocalStrategy(
    function(username, password, done) {
      User.getUserByUsername(username, function(err, user){
        if(err) throw err;
        if(!user){
          return done(null, false, {message: 'Unknown User'});
        }

        User.comparePassword(password, user.password, function(err, isMatch){
          if(err) throw err;
          if(isMatch){
            return done(null, user);
          } else {
            return done(null, false, {message: 'Invalid password'});
          }
        });
      });
    }));

passport.serializeUser(function(user, done) {
  done(null, user.id);
});

passport.deserializeUser(function(id, done) {
  User.getUserById(id, function(err, user) {
    done(err, user);
  });
});

router.post('/login',
    passport.authenticate('local', {successRedirect:'/', failureRedirect:'/users/login',failureFlash: true}),
    function(req, res) {
      res.redirect('/');
    });

router.get('/logout', function(req, res){
  req.logout();

  req.flash('success_msg', 'You are logged out');

  res.redirect('/users/login');
});

// Add Date
router.post('/add', function(req, res) {
  var date = req.body.date;
  var message = req.body.message;


  //Validation
  req.checkBody('date', 'Date is required').notEmpty();
  req.checkBody('message', 'Message is required').notEmpty();

  var errors = req.validationErrors();

  if(errors){
    res.render('add',{
      errors:errors
    });
  }else{
    var newUser = new User({
      date: date,
      message: email
    });

    User.createUser(newUser, function(err, user){
      if(err) throw err;
      console.log(User);
    });

    req.flash('success_msg', 'Your Request registered..');

    res.redirect('/users/login');
  }
});

module.exports = router;