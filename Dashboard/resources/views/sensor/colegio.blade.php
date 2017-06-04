</!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="/js/flot/excanvas.min.js"></script><![endif]-->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.time.js"></script>
<script>
var cpu = [], disk = [];
var dataset;
var totalPoints = 100;
var updateInterval = 5000;
var now = new Date().getTime();
var id = {{$id}};
var minX = 0;
var maxX = 0;
var minY = 0;
var maxY = 0;
switch(id) {
    case 2:
        var minX = 200;
        var maxX = 300;
        var minY = -100;
        var maxY = 100;
        break;
    case 3:
        var minX = 0;
        var maxX = 20;
        var minY = -100;
        var maxY = 100;
        break;
    case 4:
        var minX = 0;
        var maxX = 20;
        var minY = -100;
        var maxY = 100;
        break;
    default:
        var minX = 0;
        var maxX = 100;
        var minY = -200;
        var maxY = 200;
}
var options = {
    series: {
        lines: {
            lineWidth: 1.2
        },
        bars: {
            align: "center",
            fillColor: { colors: [{ opacity: 1 }, { opacity: 1}] },
            barWidth: 500,
            lineWidth: 1
        }
    },
    xaxis: {
        mode: "time",
        tickSize: [60, "second"],
        tickFormatter: function (v, axis) {
            var date = new Date(v);

            if (date.getSeconds() % 20 == 0) {
                var hours = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
                var minutes = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
                var seconds = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();

                return hours + ":" + minutes + ":" + seconds;
            } else {
                return "";
            }
        },
        axisLabel: "Time",
        axisLabelUseCanvas: true,
        axisLabelFontSizePixels: 12,
        axisLabelFontFamily: 'Verdana, Arial',
        axisLabelPadding: 10
    },
    yaxes: [
        {
            min: minX,
            max: maxX,
            tickSize: 5,
            tickFormatter: function (v, axis) {
                if (v % 10 == 0) {
                    return v;
                } else {
                    return "";
                }
            },
            axisLabel: "CPU loading",
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 12,
            axisLabelFontFamily: 'Verdana, Arial',
            axisLabelPadding: 6
        }, {
            min: minY,
            max: maxY,
            position: "right",
            axisLabel: "Disk",
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 12,
            axisLabelFontFamily: 'Verdana, Arial',
            axisLabelPadding: 6
        }
    ],
    legend: {
        noColumns: 0,
        position:"nw"
    },
    grid: {      
        backgroundColor: { colors: ["#ffffff", "#EDF5FF"] }
    }
};

function initData() {
    for (var i = 0; i < totalPoints; i++) {
        var temp = [now += updateInterval, 0];

        cpu.push(temp);
        disk.push(temp);
    }
}

function GetData(id) {
    $.ajaxSetup({ cache: false });

    $.ajax({
        url: "/ultimo/"+id,
        dataType: 'json',
        success: update,
        error: function () {
            setTimeout(GetData(id), updateInterval);
        }
    });
}

var temp;

function update(_data) {
    cpu.shift();
    disk.shift();

    now += updateInterval

    temp = [now, _data.valor];
    cpu.push(temp);

    temp = [now, _data.texto];
    disk.push(temp);

    dataset = [
        { label: _data.name + " Valor:" + _data.valor, data: cpu, lines: { fill: true, lineWidth: 1.2 }, color: "#00FF00" },
        { label: "Texto:" + _data.texto, data: disk, lines: { lineWidth: 1.2}, color: "#0044FF", yaxis: 2 }       
    ];

    $.plot($("#flot-placeholder1"), dataset, options);
    setTimeout(GetData(id), updateInterval);
}


$(document).ready(function () {
    initData();

    dataset = [        
        { label: "Valor:", data: cpu, lines:{fill:true, lineWidth:1.2}, color: "#00FF00" },
        { label: "Texto:", data: disk, lines: { lineWidth: 1.2}, color: "#0044FF", yaxis: 2 }
    ];

    $.plot($("#flot-placeholder1"), dataset, options);
    setTimeout(GetData(id), updateInterval);
});



</script>
</head>
<body>
<!-- HTML -->
<div id="flot-placeholder1" style="width:550px;height:300px;margin:0 auto"></div>
</body>
</html>