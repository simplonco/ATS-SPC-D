/**
 * Created by shadi on 23/08/2016.
 */
var express = require('express');
var path = require('path');
var favicon = require('serve-favicon');
var logger = require('morgan');
var cookieParser = require('cookie-parser');
var bodyParser = require('body-parser');
var MongoClient = require('mongodb').MongoClient;
var assert = require('assert');
var exphbs= require('express-handlebars');
var app = express();

var routes = require('./routes/index');
var users = require('./routes/users');




app.engine('handlebars',exphbs({defaultLayout:'main'}));
app.set('view engine','handlebars');

var hbs=exphbs.create({
    helpers:{
        foo: function() { return 'FOO!';},
        bar:function(){return 'FOO!';}
    }
})

app.get('/',function(req, res){// TODO:
   // res.render('home');
    res.render('login', {
        showTitle: false,

        // Override `foo` helper only for this rendering.
        helpers: {
            foo: function () { return 'foo.'; }
        }
    });
})


/*app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'jade');
*/
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

app.get('/check', function (req, res) {
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
module.exports = app;
