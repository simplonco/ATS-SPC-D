/**
 * Created by shadi on 25/08/2016.
 */
var express = require('express');
var app = express();
var path = require('path');
var favicon = require('serve-favicon');
var logger = require('morgan');


// Body Parser Middleware
var cookieParser = require('cookie-parser');
var bodyParser = require('body-parser');
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({extended: false}));
app.use(cookieParser());


var mongo = require('mongodb');
var mongoose = require('mongoose');
var MongoClient = require('mongodb').MongoClient;
var assert = require('assert');
mongoose.connect('mongodb://localhost/chadi');// need DataBase Name
var db = mongoose.connection;

var expressValidator = require('express-validator');
var flash = require('connect-flash');
var session = require('express-session');
var passport = require('passport');
var localStrategy = require('passport-local').Strategy;
app.use(session({
    secret:'secret',
    resave:true,
    saveUninitialized:false
}));


var routes = require('./routes/index');


app.use('/', routes);


var exphbs = require('express-handlebars');
app.set('views', path.join(__dirname, 'views'));
app.engine('handlebars', exphbs({defaultLayout: 'main'}));
app.set('view engine', 'handlebars');


// Set Static Folder
app.use(express.static(path.join(__dirname, 'public')));

// Connect Flash
//app.use(flash());


// set port
app.set('port', (process.env.PORT || 4000));
app.listen(app.get('port'), function () {
    console.log('Server started' + app.get('port'));
});


app.post('/userCheck', function (req, res) {

    var name = req.body.username;
    // console.log(name);
    MongoClient.connect('mongodb://localhost:27017/chadi', function (err, db) {

        assert.equal(err, null);
        console.log("Successfully connected to MongoDB.");

        var query = {"name": name};

        db.collection('client').find(query).toArray(function (err, docs) {
            assert.equal(err, null);
            if(docs.length== 0){// detect if the user registered in the dataBase
                res.send('user not found');
            }else{
                res.send('');
            }


        });

    });

});

module.exports = app;