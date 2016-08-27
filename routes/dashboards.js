/**
 * Created by shadi on 24/08/2016.
 */
var express = require('express');
var router = express.Router();



router.get('/dashboard', function(req, res, next) {
    res.render('dashBoard');
})

module.exports = router;