<div class="ct-chart trends-chart" id="periodTrends"></div>
<style>
    #periodTrends {
        width	: 100%;
        height	: 400px;
    }
    .trends-chart text {
        fill: #76838f;
        font-family: "Roboto", sans-serif;
    }
</style>
<script>
    var chart = AmCharts.makeChart("periodTrends", {
        "creditsPosition": "top-right",
        "type": "serial",
        "theme": "light",
        "language": "be",
        "dataDateFormat": "YYYY-MM-DD",
        "legend": {
            "useGraphSettings": true
        },
        "valueAxes": [{
            "id": "v1",
            "axisAlpha": 0,
            integersOnly: true,
            "position": "left",
            "title": "Перакладзена субтытраў"
        }],
        "graphs": {!! json_encode($graphs) !!},
        "chartScrollbar": {
            "graph": "g1",
            "oppositeAxis":false,
            "offset":30,
            "scrollbarHeight": 80,
            "backgroundAlpha": 0,
            "selectedBackgroundAlpha": 0.1,
            "selectedBackgroundColor": "#888888",
            "graphFillAlpha": 0,
            "graphLineAlpha": 0.5,
            "selectedGraphFillAlpha": 0,
            "selectedGraphLineAlpha": 1,
            "autoGridCount":true,
            "color":"#AAAAAA"
        },
        "chartCursor": {
            "pan": true,
            "valueLineEnabled": true,
            "valueLineBalloonEnabled": true,
            "cursorAlpha": 0,
            "valueLineAlpha": 0.2,
            "cursorPosition":"start"
        },
        "categoryField": "date",
        "categoryAxis": {
            "parseDates": @if ('day' == $period) true @else false @endif,
            "dashLength": 1,
            "minorGridEnabled": true,
            dateFormats: [
                {period:'DD',format:'MMMM DD'},
            ]
        },
        "dataProvider": {!! json_encode($periodTrends) !!}
    });

    chart.addListener("rendered", zoomChart);

    zoomChart();

    function zoomChart() {
        chart.zoomToIndexes(0, 7);
    }
</script>