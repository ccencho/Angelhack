@extends('layout.default')
@section('content')

    <script src="https://maps.google.com/maps/api/js?sensor=false&libraries=geometry&v=3.22&api_key=AIzaSyCs8fT717i6nWVSIEuH0Xt8u6VCLB_CzqM&key=AIzaSyCs8fT717i6nWVSIEuH0Xt8u6VCLB_CzqM">
    </script>
    <script src="{{ asset('/js/maplace.js') }}"></script>

    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Colegios</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-md-6">
                <div id="controls"></div>
                <div id="gmap-menu" style="width:100%;height:600px;"></div>
            </div>
            <div class="col-md-6">
                <div class="detallecolegio">
                    Aquí van los gráficos de los sensores del colegio
                    <div class="row">
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-6">
                            <h2><span class="label label-default" id="namesensor">Sensor de inclinación </span></h2>
                        </div>


                    </div>
                    <iframe src="/colegio/1"  style="width:100%;height:500px;" frameborder="0" scrolling="yes" id="iframe"></iframe>
                </div>
            </div>
        </div>
    {{--<div id="controls"></div>--}}
    {{--<div id="gmap-menu"></div>--}}


    <script type="text/javascript">

        $(function() {
            var colegios = [];
            var data_colegios= '<?php echo $colegios ?>' ;
            var colegios_json=jQuery.parseJSON(data_colegios);
            //colegios_json=data;
            console.log(colegios_json);
            //return false;
            var colegios = [];

            $.each(colegios_json, function(i, item) {

                var color='green';
                $.each(item.sensores, function (j,sensor) {
                    switch(sensor.color) {
                        case 2:
                            color='red';
                            break;
                        case 1:
                            if (color!=='red' )
                                color='yellow';
                            break;
                        case 0:
                            if (color!=='red' && color!=='yellow')
                                color='green';
                            break;
                    }
                });
                var colegio = new Object();
                console.log(color);
                colegio.lat = item.latitude;
                colegio.lon = item.longitude;
                colegio.html ='<h3>'+item.name+'</h3>';
                colegio.icon = '/icons/marker-icon-'+color+'.png';
                colegio.animation =  google.maps.Animation.DROP;
                colegio.id = item.id;
                colegios.push(colegio);

            });

            var myMap = new Maplace({
                locations: colegios,
                map_div: '#gmap-menu',
                controls_type: 'list ',
                controls_on_map: false,
                afterOpenInfowindow: function (index, location, marker) {
                    console.log(index);
                    console.log(location);
                    console.log(marker);
                    var id_colegio=location.id;
                    //$('#colegio').text(id_colegio);
                    var sensorname='';
                    switch (id_colegio) {
                        case 1:
                            sensorname='Sensor de inclinación';
                            break;
                        case 2:
                            sensorname='Sensor de flexión';
                            break;
                        case 3:
                            sensorname='Sensor de nivel de agua';
                            break;
                        case 4:
                            sensorname='Sensor de nivel de energía';
                            break;
                    }
                    url='/colegio/'+id_colegio;
                    $('#namesensor').text(sensorname);
                    $('#iframe').attr('src', url);
                    $('#iframe').reload();

                }
            });
            myMap.Load({locations: colegios});
            function fetchData(){
                $.get( "/colegio/listar-mapa" , function( data ) {
                    colegios_json = jQuery.parseJSON(data);
                    var colegios = [];

                    $.each(colegios_json, function(i, item) {

                        var color='green';
                        $.each(item.sensores, function (j,sensor) {
                            switch(sensor.color) {
                                case 2:
                                    color='red';
                                    break;
                                case 1:
                                    if (color!=='red' )
                                        color='yellow';
                                    break;
                                case 0:
                                    if (color!=='red' && color!=='yellow')
                                        color='green';
                                    break;
                            }
                        });
                        var colegio = new Object();
                        console.log(color);
                        colegio.lat = item.latitude;
                        colegio.lon = item.longitude;
                        colegio.html ='<h3>'+item.name+'</h3>';
                        colegio.icon = '/icons/marker-icon-'+color+'.png';
                        colegio.animation =  google.maps.Animation.DROP;
                        colegio.id = item.id;
                        colegios.push(colegio);
                    });
                    //return false;
                    if (myMap.Loaded()) {
                        myMap.RemoveLocations(1);
                        //myMap.SetLocations(colegios).Load();
                        myMap.Load({locations: colegios});
                    } else {
                        myMap.Load();
                    }
                    /*if (myMap.Loaded()) {
                        myMap.RemoveLocations(1);
                        myMap.Load({locations: colegios});
                    } else {
                        myMap.Load({locations: colegios});
                    }*/
                });

            }

            fetchData();
            setInterval(function(){
                fetchData();

                //myMap.SetLocations(colegios).Load();
            },5000);


        });
    </script>
@stop