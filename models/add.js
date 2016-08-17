var mongoose = require('mongoose');

// User Schema
var UserSchema = mongoose.Schema({
    date: {
        type: String
    },
    message: {
        type: String
    }
});

var User = module.exports = mongoose.model('User', UserSchema);

module.exports.getUserBydate = function(date, callback){
    var query = {date: date};
    User.findOne(query, callback);
}

module.exports.getUserByMessage = function(message, callback){
    var query = {message: message};
    User.findOne(query, callback);
}