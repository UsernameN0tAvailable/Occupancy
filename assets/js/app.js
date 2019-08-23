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
    let weekDayUrl = "/week_day_range_stats";
    let currentLocation = '/von_roll';
    let currentDate = '2019-07-07';
    let currentRange = '2019-07-07 - 2019-07-08';
    let subPage = '/c';

    singleDatePicker();

    $('#location_menu a').click(function(event){
        event.preventDefault();
        currentLocation = $(this).attr('href');

        switch(subPage){
            case '/c':
                singleDatePicker();
                createCalendarChart();
                break;
            case '/t':
                rangeDatePicker();
                createTrendsChart();
                break;
            case '/d':
                rangeDatePicker();
                createWeekDayChart();
                break;
        }
        $('.table-active').removeClass('table-active');
        $('#' + subPage.substring(1)).addClass('table-active');
        $('#' + currentLocation.substring(1)).addClass('table-active');
    });


    $('#kal_trends a').click(function(event){
        event.preventDefault();
        subPage = $(this).attr('href');

        switch(subPage){
            case '/c':
                singleDatePicker();
                createCalendarChart();
                break;
            case '/t':
                rangeDatePicker();
                createTrendsChart();
                break;
            case '/d':
                rangeDatePicker();
                createWeekDayChart();
                break;
        }

        $('.table-active').removeClass('table-active');
        $('#' + currentLocation.substring(1)).addClass('table-active');
        $('#' + subPage.substring(1)).addClass('table-active');
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
        },
        'graphC' : {
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


    function createWeekDayChart(){
        if(currentDate != null && currentLocation != null) {
            range = currentRange.split(' - ');
            let url = weekDayUrl + currentLocation + '/' + range[0] + '/' + range[1];

            $('.app-chartist').each(function () {
                let el = $(this)[0];
                let option = options['graphC'];
                $.get({url: url, cache: false}, function (data) {
                    new Chartist.Bar(el, data, option);
                });
            });
        }
    }

    function singleDatePicker(){
        $('#picker').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            timePicker24Hour: true,
            startDate: currentDate,
            endDate: currentDate,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
    }

    function rangeDatePicker(){

        range = currentRange.split(' - ');

        $('#picker').daterangepicker({
            autoApply: true,
            startDate: range[0],
            endDate: range[1],
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
    }


    $("#picker").change(function(){
        switch(subPage){
            case '/c':
                currentDate = $("#picker").val();
                createCalendarChart();
                break;
            case '/t':
                currentRange = $("#picker").val();
                createTrendsChart();
                break;
            case '/d':
                currentRange = $("#picker").val();
                createWeekDayChart();
                break;
        }
    });
});




