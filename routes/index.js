var express = require('express');
var router = express.Router();
var MongoClient = require('mongodb').MongoClient;
var assert = require('assert');


/* GET home page. */
router.get('/', function (req, res, next) {
    res.render('login');
});
/*
 router.post('/user', function (req, res, next) {
 var name = req.body.username;
 var password = req.body.password;
 console.log(name + '' + password);

 MongoClient.connect('mongodb://localhost:27017/chadi', function (err, db) {

 assert.equal(err, null);
 //   console.log("Successfully connected to MongoDB.");

 var query = {"name": name, "password": password};

 db.collection('client').find(query).toArray(function (err, docs) {

 assert.equal(err, null);
 //    assert.notEqual(docs.length, 0);
 if (docs.length == 0) {// detect if the user registered in the dataBase
 console.log('user not found');
 res.render('login');
 //TODO : redirect the page to the login form  (use next to other function) ?
 } else {
 docs.forEach(function (doc) {

 if (doc.admin) {
 console.log(doc.name + " is a: " + doc.admin + " :  ADMIN.");
 res.render('admin');
 //TODO : redirect the page to the ADMIN
 } else {
 console.log('normal user');
 //TODO : redirect the page to the User
 res.render('user');
 }
 });
 }


 db.close();

 });


 });

 });
 */

//TODO: cant render with post just with get try way

/*
 router.get('/user', function (req, res) {


 var name = req.query.username;
 var password = req.query.password;
 console.log(name + '' + password);

 MongoClient.connect('mongodb://localhost:27017/chadi', function (err, db) {

 assert.equal(err, null);
 //   console.log("Successfully connected to MongoDB.");

 var query = {"name": name, "password": password};

 db.collection('client').find(query).toArray(function (err, docs) {

 assert.equal(err, null);
 //    assert.notEqual(docs.length, 0);
 if (docs.length == 0) {// detect if the user registered in the dataBase
 console.log('user not found');
 res.render('login');
 //TODO : redirect the page to the login form  (use next to other function) ?
 } else {
 docs.forEach(function (doc) {

 if (doc.admin) {
 console.log(doc.name + " is a: " + doc.admin + " :  ADMIN.");
 res.render('admin');
 //TODO : redirect the page to the ADMIN
 } else {
 console.log('normal user');
 //TODO : redirect the page to the User
 res.render('user');
 }
 });
 }


 db.close();

 });


 });
 }) ;
 */

router.post('/user', function (req, res, next) {
    var name = req.body.username;
    var password = req.body.password;
    console.log(name + '' + password);

    MongoClient.connect('mongodb://localhost:27017/chadi', function (err, db) {

        assert.equal(err, null);
        console.log("Successfully connected to MongoDB.");

        var query = {"name": name, "password": password};

        db.collection('client').find(query).toArray(function (err, docs) {

            assert.equal(err, null);
            //    assert.notEqual(docs.length, 0);
            if (docs.length == 0) {// detect if the user registered in the dataBase
                console.log('user not found');
res.send('/login');
                //TODO : redirect the page to the login form  (use next to other function) ?
            } else {
                docs.forEach(function (doc) {

                    if (doc.admin) {
                        console.log(doc.name + " is a: " + doc.admin + " :  ADMIN.");
res.send('/admin');
                        //TODO : redirect the page to the ADMIN
                    } else {
                        console.log('normal user');
                        //TODO : redirect the page to the User
                res.send('/user');
                    }
                });
            }


            db.close();

        });





    });
});


//TODO:
/*
 assert.equal(err, null);
 var query = {"name": name };
 var cursor = db.collection('client').find(query);
 cursor.forEach(function (doc) {
 console.log(doc.name+"  is a :" +doc.admin+"  admin");
 },
 function(err){
 assert.equal(err, null);
 return db.close();
 }, function (err) {
 assert.equal(err, null);
 return db.close();
 });
 */
//  if (match.admin == true) {//check if the user is admin
//  TODO : index render to RH page
//   console.log(match.name + ' : this user is Admin');
//res.render('admin');// make  admin  page to render it
//   } else {
//   console.log('this user is Client');
//  TODO : index render to USER
//res.render('user');//  make user page to render it


//

/*
 router.get('/user',function(req, res, next ) {
 var name = req.query.username;
 var password = req.query.password;
 console.log(name + '' + password);
 MongoClient.connect('mongodb://localhost:27017/chadi', function (err, db) {
 assert.equal(err, null);
 var query = {"name": name}
 var cursor = db.collection('client').find(query);
 cursor.forEach(function (doc) {


 console.log(doc.name+"is a :" +doc.admin+"admin");
 },
 function(err){
 assert.equal(err, null);
 return db.close();
 //  if (match.admin == true) {//check if the user is admin
 //  TODO : index render to RH page
 //   console.log(match.name + ' : this user is Admin');
 //res.render('admin');// make  admin  page to render it
 //   } else {
 //   console.log('this user is Client');
 //  TODO : index render to USER
 //res.render('user');//  make user page to render it


 }, function (err) {
 assert.equal(err, null);
 return db.close();

 });
 res.end();
 //
 });
 });
 */
router.post('/userdata',function() {
    MongoClient.connect('mongodb://localhost:27017/chadi', function(err, db){
        assert.equal(err,null);
        db.collection('client').find().toArray(function(err,docs){
            assert.equal(err, null);

        })
    })
})
module.exports = router;
