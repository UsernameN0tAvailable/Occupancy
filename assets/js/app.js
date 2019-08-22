/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.scss in this case)
require('../css/app.scss');
require('chartist/dist/chartist.css');
require('chartist-plugin-legend');
require('daterangepicker');
require('daterangepicker/daterangepicker.css')
require('popper.js');
const Chartist = require('chartist');
window.Chartist = Chartist;

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');
window.$ = $;



// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

//import 'bootstrap';


// create global $ and jQuery variables
//global.$ = global.jQuery = $;

// or you can include specific pieces
//require('bootstrap/js/dist/tooltip');
//require('bootstrap/js/dist/popover');


$(document).ready(function () {
    let calendarUrl = "/data";
    let trendsUrl = "/range_stats";
    let currentLocation = '/von_roll';
    let currentDate = '2019-07-07';
    let currentRange = '2019-07-07 - 2019-07-08';
    let isCalendar = true;

    singleDatePicker();

    $('#location_menu a').click(function(event){
        event.preventDefault();
        currentLocation = $(this).attr('href');
        if(isCalendar) {
            createCalendarChart();
        }else{
            createTrendsChart();
        }
    });


    $('#kal_trends a').click(function(event){
        event.preventDefault();

        isCalendar = ($(this).attr('href') === '/calendar');

        if(isCalendar){
            singleDatePicker();
            createCalendarChart();
        }else{
            rangeDatePicker();
            createTrendsChart();
        }
    });


    let options = {
        'graphA': {
            showPoint: false,
            axisX:
                {
                    labelInterpolationFnc: function skipLabels(value, index) {
                        return index % 60 === 0 ? value : null;
                    }
                },
            plugins: [Chartist.plugins.legend()]
        },
        'graphB': {
            showPoint: false,
            showArea: true,
            showLine: false,
            axisX:
                {
                    labelInterpolationFnc: function skipLabels(value, index) {
                        return index % 60 === 0 ? value : null;
                    }
                },
            plugins: [Chartist.plugins.legend()]
        }
    };

    function createCalendarChart() {
        if(currentDate != null && currentLocation != null) {

            let url = calendarUrl + currentLocation + '/' + currentDate;

            $('.app-chartist').each(function () {
                let el = $(this)[0];
                let option = options[$(this).data('option')];
                $.get({url: url, cache: false}, function (data) {
                    new Chartist.Line(el, data, option);
                });
            });
        }
    }

    function createTrendsChart(){
        if(currentDate != null && currentLocation != null) {
            range = currentRange.split(' - ');
            let url = trendsUrl + currentLocation + '/' + range[0] + '/' + range[1];

            $('.app-chartist').each(function () {
                let el = $(this)[0];
                let option = options[$(this).data('option')];
                $.get({url: url, cache: false}, function (data) {
                    new Chartist.Line(el, data, option);
                });
            });
        }
    }

    function singleDatePicker(){
        $('#picker').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            timePicker24Hour: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
    }

    function rangeDatePicker(){
        $('#picker').daterangepicker({
            autoApply: true,
            startDate: '2019/07/07',
            endDate: '2019/07/08',
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
    }


    $("#picker").change(function(){
        if (isCalendar) {
            currentDate = $("#picker").val();
            createCalendarChart();
        }else{
            currentRange = $("#picker").val();
            createTrendsChart();
        }
    });
});




