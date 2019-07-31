/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');
require('chartist/dist/chartist.css');
const Chartist = require('chartist');
window.Chartist = Chartist;

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');
window.$ = $;

// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');





// create global $ and jQuery variables
//global.$ = global.jQuery = $;

// or you can include specific pieces
 //require('bootstrap/js/dist/tooltip');
 //require('bootstrap/js/dist/popover');



$(document).ready(function() {
    $('[data-toggle="popover"]').popover();


    $('.app-chartist').each(function(){
        let url = $(this).data('url');
        let el = $(this)[0];
        $.get(url, function(data){
            new Chartist.Line(el, data);
        });


    });
});




