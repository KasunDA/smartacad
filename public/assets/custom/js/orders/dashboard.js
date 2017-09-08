/**
 * Created by Kheengz on 9/4/2017.
 */

jQuery(document).ready(function() {
    // Ajax Get Academic Terms Based on the Academic Year
    getDependentListBox($('#academic_year_id'), $('#academic_term_id'), '/list-box/academic-term/');
});

var ChartsPaidItems = function() {

    var initChartPaidItems = function(termId) {

        $.ajax({
            type: "GET",
            url: '/orders/paid-items/' + termId,
            success: function(data){
                try{
                    $('#order_items_stats_loading').hide();
                    $('#order_items_stats_content').show();

                    var chart = AmCharts.makeChart("order_items", {
                        "theme": "light",
                        "type": "serial",
                        "startDuration": 2,

                        "fontFamily": 'Open Sans',

                        "color":    '#888',

                        "dataProvider": data,
                        "valueAxes": [{
                            "position": "left",
                            "axisAlpha": 0,
                            "gridAlpha": 0
                        }],
                        "graphs": [{
                            "balloonText": "<b>[[category]]: [[value]] Generated</b>",
                            "colorField": "color",
                            "fillAlphas": 0.85,
                            "lineAlpha": 0.1,
                            "type": "column",
                            "topRadius": 1,
                            "valueField": "amount"
                        }],
                        "depth3D": 40,
                        "angle": 30,
                        "chartCursor": {
                            "categoryBalloonEnabled": false,
                            "cursorAlpha": 0,
                            "zoomable": false
                        },
                        "categoryField": "item",
                        "categoryAxis": {
                            "gridPosition": "start",
                            "axisAlpha": 0,
                            "gridAlpha": 0

                        },
                        "exportConfig": {
                            "menuTop": "20px",
                            "menuRight": "20px",
                            "menuItems": [{
                                "icon": '/lib/3/images/export.png',
                                "format": 'png'
                            }]
                        }
                    }, 0);

                    $('#order_items').closest('.portlet').find('.fullscreen').click(function() {
                        chart.invalidateSize();
                    });
                } catch (exception) {
                    $('#order_items').html('<div class="info-box  bg-info-dark  text-white"><div class="info-details"><h4>'+data+'</h4></div></div>');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $('#order_items').html(errorThrown);
            }
        });
    };

    return {
        //main function to initiate the module
        init: function(termId) {
            initChartPaidItems(termId);
        }
    };

}();
var ChartsPendingItems = function() {

    var initChartPendingItems = function(termId) {

        $.ajax({
            type: "GET",
            url: '/orders/pending-items/' + termId,
            success: function(data){
                try{
                    $('#order_items_pending_stats_loading').hide();
                    $('#order_items_pending_stats_content').show();

                    var chart = AmCharts.makeChart("order_items_pending", {
                        "theme": "light",
                        "type": "serial",
                        "startDuration": 2,

                        "fontFamily": 'Open Sans',

                        "color":    '#888',

                        "dataProvider": data,
                        "valueAxes": [{
                            "position": "left",
                            "axisAlpha": 0,
                            "gridAlpha": 0
                        }],
                        "graphs": [{
                            "balloonText": "<b>[[category]]: [[value]] Pending</b>",
                            "colorField": "color",
                            "fillAlphas": 0.85,
                            "lineAlpha": 0.1,
                            "type": "column",
                            "topRadius": 1,
                            "valueField": "amount"
                        }],
                        "depth3D": 40,
                        "angle": 30,
                        "chartCursor": {
                            "categoryBalloonEnabled": false,
                            "cursorAlpha": 0,
                            "zoomable": false
                        },
                        "categoryField": "item",
                        "categoryAxis": {
                            "gridPosition": "start",
                            "axisAlpha": 0,
                            "gridAlpha": 0

                        },
                        "exportConfig": {
                            "menuTop": "20px",
                            "menuRight": "20px",
                            "menuItems": [{
                                "icon": '/lib/3/images/export.png',
                                "format": 'png'
                            }]
                        }
                    }, 0);

                    $('#order_items_pending').closest('.portlet').find('.fullscreen').click(function() {
                        chart.invalidateSize();
                    });
                } catch (exception) {
                    $('#order_items_pending').html('<div class="info-box  bg-info-dark  text-white"><div class="info-details"><h4>'+data+'</h4></div></div>');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $('#order_items_pending').html(errorThrown);
            }
        });
    };

    return {
        //main function to initiate the module
        init: function(termId) {
            initChartPendingItems(termId);
        }
    };

}();

var ChartsExpectedItems = function() {

    var initChartPendingItems = function(termId) {

        $.ajax({
            type: "GET",
            url: '/orders/expected-items/' + termId,
            success: function(data){
                try{
                    $('#order_items_expected_stats_loading').hide();
                    $('#order_items_expected_stats_content').show();

                    var chart = AmCharts.makeChart("order_items_expected", {
                        "theme": "light",
                        "type": "serial",
                        "startDuration": 2,

                        "fontFamily": 'Open Sans',

                        "color":    '#888',

                        "dataProvider": data,
                        "valueAxes": [{
                            "position": "left",
                            "axisAlpha": 0,
                            "gridAlpha": 0
                        }],
                        "graphs": [{
                            "balloonText": "<b>[[category]]: [[value]] Expected</b>",
                            "colorField": "color",
                            "fillAlphas": 0.85,
                            "lineAlpha": 0.1,
                            "type": "column",
                            "topRadius": 1,
                            "valueField": "amount"
                        }],
                        "depth3D": 40,
                        "angle": 30,
                        "chartCursor": {
                            "categoryBalloonEnabled": false,
                            "cursorAlpha": 0,
                            "zoomable": false
                        },
                        "categoryField": "item",
                        "categoryAxis": {
                            "gridPosition": "start",
                            "axisAlpha": 0,
                            "gridAlpha": 0

                        },
                        "exportConfig": {
                            "menuTop": "20px",
                            "menuRight": "20px",
                            "menuItems": [{
                                "icon": '/lib/3/images/export.png',
                                "format": 'png'
                            }]
                        }
                    }, 0);

                    $('#order_items_expected').closest('.portlet').find('.fullscreen').click(function() {
                        chart.invalidateSize();
                    });
                } catch (exception) {
                    $('#order_items_expected').html('<div class="info-box  bg-info-dark  text-white"><div class="info-details"><h4>'+data+'</h4></div></div>');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $('#order_items_expected').html(errorThrown);
            }
        });
    };

    return {
        //main function to initiate the module
        init: function(termId) {
            initChartPendingItems(termId);
        }
    };

}();
