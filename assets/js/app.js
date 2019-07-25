require('../css/bootstrap.min.css');
require('../css/app.css');
require('../css/swipe.min.css');

require('popper.js');
require('./bootstrap.min.js');
const $ = require('jquery');

$(document).ready(function(event) {
    $('#loading').hide();
    $('#content').css({display: 'flex'});
    $('[data-toggle="tooltip"]').tooltip();
});