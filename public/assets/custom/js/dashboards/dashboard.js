jQuery(document).ready(function() {
    // MORRIS CHARTS DEMOS
    // BAR CHART
    $.ajax({
        type: "GET",
        url: '/dashboard/students-classlevel',
        success: function(data){
            try{
                console.log(data);
                new Morris.Bar({
                    element: 'student-classlevel',
                    data: data,
                    xkey: 'y',
                    ykeys: ['a'],
                    labels: ['Students']
                });
            } catch (exception) {
                $('#student-classlevel').html('<div class="info-box  bg-info-dark  text-white"><div class="info-details"><h4>'+data+'</h4></div></div>');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#chart1').html(errorThrown);
        }
    });
});


var ChartsFlotcharts = function() {

    return {
        //main function to initiate the module
        initPieCharts: function() {

            // Student Gender
            if ($('#student_gender').size() !== 0) {
                $.ajax({
                    type: "GET",
                    url: '/dashboard/students-gender',
                    success: function (data) {
                        //console.log(data);
                        $.plot($("#student_gender"), data, {
                            series: {
                                pie: {
                                    show: true,
                                    radius: 0.8,
                                    label: {
                                        show: true,
                                        radius: 3 / 4,
                                        formatter: function(label, series) {
                                            return '<div style="font-size:10pt;text-align:center;padding:2px;color:white;">' + label + '(' + series.value + ')<br/>' + Math.round(series.percent) + '%</div>';
                                        },
                                        background: {
                                            opacity: 0.8,
                                            color: '#000'
                                        }
                                    }
                                }
                            },
                            legend: {
                                show: false
                            }
                        });
                    }
                });
            }
        }
    };
}();

var ChartsAmcharts = function() {

    var initChartSample5 = function() {
        var chart = AmCharts.makeChart("chart_5", {
            "theme": "light",
            "type": "serial",
            "startDuration": 2,

            "fontFamily": 'Open Sans',

            "color":    '#888',

            "dataProvider": [{
                "country": "USA",
                "visits": 4025,
                "color": "#FF0F00"
            }, {
                "country": "China",
                "visits": 1882,
                "color": "#FF6600"
            }, {
                "country": "Japan",
                "visits": 1809,
                "color": "#FF9E01"
            }, {
                "country": "Germany",
                "visits": 1322,
                "color": "#FCD202"
            }, {
                "country": "UK",
                "visits": 1122,
                "color": "#F8FF01"
            }, {
                "country": "France",
                "visits": 1114,
                "color": "#B0DE09"
            }, {
                "country": "India",
                "visits": 984,
                "color": "#04D215"
            }, {
                "country": "Spain",
                "visits": 711,
                "color": "#0D8ECF"
            }, {
                "country": "Netherlands",
                "visits": 665,
                "color": "#0D52D1"
            }, {
                "country": "Russia",
                "visits": 580,
                "color": "#2A0CD0"
            }, {
                "country": "South Korea",
                "visits": 443,
                "color": "#8A0CCF"
            }, {
                "country": "Canada",
                "visits": 441,
                "color": "#CD0D74"
            }, {
                "country": "Brazil",
                "visits": 395,
                "color": "#754DEB"
            }, {
                "country": "Italy",
                "visits": 386,
                "color": "#DDDDDD"
            }, {
                "country": "Australia",
                "visits": 384,
                "color": "#999999"
            }, {
                "country": "Taiwan",
                "visits": 338,
                "color": "#333333"
            }, {
                "country": "Poland",
                "visits": 328,
                "color": "#000000"
            }],
            "valueAxes": [{
                "position": "left",
                "axisAlpha": 0,
                "gridAlpha": 0
            }],
            "graphs": [{
                "balloonText": "[[category]]: <b>[[value]]</b>",
                "colorField": "color",
                "fillAlphas": 0.85,
                "lineAlpha": 0.1,
                "type": "column",
                "topRadius": 1,
                "valueField": "visits"
            }],
            "depth3D": 40,
            "angle": 30,
            "chartCursor": {
                "categoryBalloonEnabled": false,
                "cursorAlpha": 0,
                "zoomable": false
            },
            "categoryField": "country",
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

        jQuery('.chart_5_chart_input').off().on('input change', function() {
            var property = jQuery(this).data('property');
            var target = chart;
            chart.startDuration = 0;

            if (property == 'topRadius') {
                target = chart.graphs[0];
            }

            target[property] = this.value;
            chart.validateNow();
        });

        $('#chart_5').closest('.portlet').find('.fullscreen').click(function() {
            chart.invalidateSize();
        });
    }

    return {
        //main function to initiate the module
        init: function() {
            initChartSample5();
        }

    };

}();

jQuery(document).ready(function() {    
    ChartsFlotcharts.initPieCharts();
    ChartsAmcharts.init();
});