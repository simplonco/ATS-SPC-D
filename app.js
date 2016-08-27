var express = require('express');
var path = require('path');
var favicon = require('serve-favicon');
var logger = require('morgan');
var cookieParser = require('cookie-parser');
var bodyParser = require('body-parser');
var MongoClient = require('mongodb').MongoClient;
var assert = require('assert');
var app = express();
var session=require('express-session');
var routes = require('./routes/index');
var users = require('./routes/users');

app.get('/login', function (req, res) {
    var name = req.query.username;
    var password = req.query.password;
    console.log(name + '' + password);
    MongoClient.connect('mongodb://localhost:27017/chadi', function (err, db) {
        assert.equal(err, null);
        var query = {"name": name, "password": password}
        var cursor = db.collection('client').find(query);
        cursor.forEach(function (doc) {
            var match = doc;
            if (match.admin == true) {//check if the user is admin
                //  TODO : index render to RH page
                console.log(match.name + ' : this user is Admin');

            } else {
                console.log('this user is Client');
                //  TODO : index render to USER
            }
        }, function (err) {
            assert.equal(err, null);
            return db.close();

        });
        res.end();
        /*
         if (err){
         console.log('error');
         }else{
         console.log(doc);

         } );*/
//db.collection("client").insertOne(query,function(err, res){
        // console.log("inserted :".res.insertedId+ "\n");
    })
});
// Express Session
app.use(session({
    secret: 'secret',
    saveUninitialized: true,
    resave: true
}));
/*MongoClient.connect('mongodb://localhost:27017/chadi',function (err, db) {
 assert.equal(err, null);
var query = {"": ""};
 var cursor = db.collection('client').find();
 var result;
 cursor.forEach(
 function (doc) {
 result+="name " + doc.name + " Has password" + doc.password + "admin" + doc.admin ;
 console.log("name " + doc.name + " Has password" + doc.password + "admin" + doc.admin);
 },
 function (err) {
 assert.equal(err, null);
 return db.close();
 }
 );
 console.log(result);
 })
 */
/*db.collection('companies').find(query).toArray(function(err,docs){
 assert.equal(err, null);
 assert.notEqual(docs.length, 0);

 docs.forEach(function(doc) {
 console.log( doc.name + " is a " + doc.category_code + " company." );
 });
 db.close();
 });
 })
 */
// view engine setup
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'jade');

// uncomment after placing your favicon in /public
//app.use(favicon(path.join(__dirname, 'public', 'favicon.ico')));
app.use(logger('dev'));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({extended: false}));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'public')));

app.use('/', routes);
app.use('/users', users);


app.listen(3000, function () {
    console.log('hello');
})

/*
 // catch 404 and forward to error handler
 app.use(function(req, res, next) {
 var err = new Error('Not Found');
 err.status = 404;
 next(err);
 });
 // error handlers
 // development error handler
 // will print stacktrace
 if (app.get('env') === 'development') {
 app.use(function(err, req, res, next) {
 res.status(err.status || 500);
 res.render('error', {
 message: err.message,
 error: err
 });
 });
 }

 // production error handler
 // no stacktraces leaked to user
 app.use(function(err, req, res, next) {
 res.status(err.status || 500);
 res.render('error', {
 message: err.message,
 error: {}
 });
 });*/
app.get('/result', function () {
    MongoClient.connect('mongodb://localhost:27017/chadi', function (err, db) {
        assert.equal(err, null);
        // var query = {"name": name, "password": password}
        var cursor = db.collection('students').find();
        cursor.skip(6);
        cursor.limit(2);
        cursor.sort({"grade": 1});
        console.log(cursor);
    });
});

module.exports = app;
