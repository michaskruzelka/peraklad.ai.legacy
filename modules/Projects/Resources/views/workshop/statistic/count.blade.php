<div class="line-chart">
    <div class="chart-header">
        <h3 class="blue-grey-700">КОЛЬКАСЦЬ СУБТЫТРАЎ</h3>
    </div>
    <div class="ct-chart trends-chart" id="subtitlesCount"></div>
</div>
<style>
    #subtitlesCount {
        width	: 100%;
        height	: 400px;
    }
</style>
<script>
    var chart2 = AmCharts.makeChart( "subtitlesCount", {
        "type": "pie",
        "theme": "light",
        "creditsPosition": "top-right",
        "dataProvider": [ {
            "status": "Перакладзена",
            "count": {{ $counts['saved'] }},
            "color": "#A2CAEE"
        }, {
            "status": "Перакладаецца",
            "count": {{ $counts['underway'] }},
            "color": "#BDBDBD"
        }, {
            "status": "Без перакладу",
            "count": {{ $counts['clean'] }},
            "color": "#F6BE80"
        } ],
        "valueField": "count",
        "titleField": "status",
        "colorField": "color",
        "balloon":{
            "fixedPosition":true
        },
        labelText: "[[title]]: [[value]]",
        balloonText: "[[title]]: [[value]] ([[percents]]%)",
        percentFormatter: {
            precision:0
        }
    } );
</script>