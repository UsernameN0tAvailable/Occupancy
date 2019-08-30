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
require('daterangepicker/daterangepicker.css');
require('popper.js');

const Chartist = require('chartist');
window.Chartist = Chartist;

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');
window.$ = $;


//
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
    let currentRange = '2019-07-07 - 2019-07-08';
    let subPage = '/c';
    let currentDate = '';
    let exp_bib = 'von_roll';
    let exp_int = '1';

    // get today's date
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = yyyy + '-' + mm + '-' + dd;
    currentDate = today;

    singleDatePicker();

    $('#location_menu a').click(function (event) {
        event.preventDefault();
        currentLocation = $(this).attr('href');

        switch (subPage) {
            case '/c':
                adjustGraphDiv(true);
                currentDate = today;
                singleDatePicker();
                createCalendarChart();
                break;
            case '/t':
                adjustGraphDiv(true);
                rangeDatePicker();
                createTrendsChart();
                break;
            case '/d':
                adjustGraphDiv(true);
                rangeDatePicker();
                createWeekDayChart();
                break;
            case '/e':
                adjustGraphDiv(false);
                rangeDatePicker();
                break;
        }
        $('.table-active').removeClass('table-active');
        if (subPage != '/e') {
            $('#' + currentLocation.substring(1)).addClass('table-active');
        }
        $('#' + subPage.substring(1)).addClass('table-active');

    });


    $('#kal_trends a').click(function (event) {
        event.preventDefault();
        subPage = $(this).attr('href');

        switch (subPage) {
            case '/c':
                adjustGraphDiv(true);
                singleDatePicker();
                createCalendarChart();
                break;
            case '/t':
                adjustGraphDiv(true);
                rangeDatePicker();
                createTrendsChart();
                break;
            case '/d':
                adjustGraphDiv(true);
                rangeDatePicker();
                createWeekDayChart();
                break;
            case '/e':
                adjustGraphDiv(false);
                rangeDatePicker();
                break;
        }

        $('.table-active').removeClass('table-active');
        if (subPage != '/e') {
            $('#' + currentLocation.substring(1)).addClass('table-active');
        }
        $('#' + subPage.substring(1)).addClass('table-active');
    });


    let options = {
        'graphA': {
            showPoint: false,
            axisX:
                {
                    labelInterpolationFnc: function skipLabels(value, index) {
                        return index % 60 === 0 ? value.substring(0, value.length - 6) : null;
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
        'graphC': {
            plugins: [Chartist.plugins.legend()]
        }
    };

    function createCalendarChart() {
        if (currentDate != null && currentLocation != null) {

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

    function createTrendsChart() {
        if (currentDate != null && currentLocation != null) {
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


    function createWeekDayChart() {
        if (currentDate != null && currentLocation != null) {
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

    function singleDatePicker() {
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

    function rangeDatePicker() {

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


    function adjustGraphDiv(isGraph) {


        let bib_html =
            '<nav><div class="row mt-5 mb-5"><ul id="location_exp" class="container btn-group col-lg-5 col-md-5" >\n' +
            '<li class="btn btn-secondary" value="bib von_roll">Von Roll</li>' +
            '<li class="btn btn-secondary active" value="bib bto"> BTO</li>' +
            '<li class="btn btn-secondary" value="bib jbb"> JBB</li>' +
            '<li class="btn btn-secondary" value="bib bmu_og"> BMÜ OG</li>' +
            '<li class="btn btn-secondary" value="bib bmu_ug"> BMÜ UG</li>' +
            '</ul> </div> </nav>';

        let freq_html = '<p class="text-center">Abtastung in Minuten</p>' +
            '<div class="row mt-2 mb-5" >' +
            '<ul id="interval" class="container btn-group col-lg-5 col-md-5">\n' +
            '<li class="btn btn-secondary active" value="int 1"> 1</input>' +
            '<li class="btn btn-secondary" value="int 5"> 5</li>' +
            '<li class="btn btn-secondary" value="int 10"> 10</li>' +
            '<li class="btn btn-secondary" value="int 20"> 20</li>' +
            '<li class="btn btn-secondary" value="int 30"> 30</li>' +
            '<li class="btn btn-secondary" value="int 60"> 60</li>' +
            '</ul>' +
            '</div> <div class="container mt-5 mb-5 text-center" style="width: 400px">\n' +
            '<button id="export_btn" type="button" class="btn btn-primary" >Export</button>\n' +
            '</div>';

        if (isGraph) {
            $('#main_graph').html('');
            $('#bib').html('');
            $('#main_graph').addClass('app-chartist');
            $('#main_graph').removeClass('text-center');
        } else {
            $('#main_graph').removeClass('app-chartist');
            $('#main_graph').addClass('text-center');
            $('#main_graph').html(freq_html);
            $('#bib').html(bib_html)
        }
    }

    $('#location a').click(function (event) {
        event.preventDefault();

        console.log('yoo');
    });


    //export button
    $(document).on('click', '.btn-primary' , function () {
        let range = currentRange.split(' - ');
        let exp_url = '/export/' + exp_bib + '/' + range[0] + '/' + range[1] + '/' + exp_int;
        location.href = exp_url;
    });




    // export "form" clicked
    $(document).on('click', '.btn-secondary' , function () {

        let cat = $(this).attr('value').split(' ')[0];
        let value = $(this).attr('value').split(' ')[1];

        if(cat === 'bib'){
            exp_bib = value;
        }else if(cat === 'int'){
            exp_int = value;
        }else{
            console.log('we have a problem!!');
        }

        //adjust active button
        $('.btn-secondary').each(function(){

            let value = $(this).attr('value').split(' ')[1];
            $(this).removeClass('active');

            if(value === exp_int || value === exp_bib){
                $(this).addClass('active');
            }
        });

    });




    $("#picker").change(function () {
        switch (subPage) {
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
            case '/e':
                currentRange = $("#picker").val();
                break;
        }
    });
});




