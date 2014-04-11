@extends('layouts.cpanel')
@section('title','Home')
@section('body_class','cpanel home')

@section('bottom_css')
	@parent
	<link rel="stylesheet" href="{{ asset('assets/css/cpanel.css') }}">	
	<link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
	<link rel="stylesheet" href="{{ asset('packages/leaflet/leaflet.css') }}">
@stop

@section('bottom_js')
	@parent
	<script src="{{ asset('packages/moment/moment-with-langs.min.js') }}"></script>
	<script src="{{ asset('packages/leaflet/leaflet.js'); }}"></script>
	<script>
	jQuery(function($){
		
		// disabled hash reference
		$('[href="#"]').on('click', function(e){
			e.preventDefault();
		});

		//////////////////////////////////////////////////////////////////////////////////////
		// Map-init
		//////////////////////////////////////////////////////////////////////////////////////
		<?php 
		$script = 'var streetFeatures = [';
		$scriptContent = array();

		foreach($streets as $street) {
			$content = '{';

			$content .= '"type": "Feature",';
			$content .= '"id": '. $street->gid .',';
			$content .= '"geometry": '. $street->geo_json . ',';
			$content .= '"properties": {"name":"'. $street->street_name.'", "direction":"'. $street->dir .'"}';

			$content .= '}';

			array_push($scriptContent, $content);
		}
		$script .= implode(',', $scriptContent);

		$script .= '];';

		echo $script;
		?>

		var streets = {
			"type": "FeatureCollection",
			"features": streetFeatures
		};

		// begin drawing map
		var map = L.map('map', {
			center: [-6.98250, 110.43011],
			zoom: 13,
			maxZoom: 16,
			minZoom:13
		});

		// set marker position
		@if(Input::has('lat') && Input::has('lng'))
		var markerPosition = [{{ Input::get('lat') }}, {{ Input::get('lng') }}];
		@else
		var markerPosition = map.getCenter();
		@endif

		map.setView(markerPosition, 13);

		// draw the road
		var roads = L.geoJson(streets,{
			style: {
				color: "orange",
				weight: 3,
				opacity: 1
			}
		}).addTo(map);

		// custom icon
		var iconSize = [30, 30];

		var icon_P = L.icon({
			iconUrl: "{{ asset('assets/img/marker/police.png') }}",
			iconSize: iconSize,
			iconAnchor: [15,30]
		}), icon_M = L.icon({
			iconUrl: "{{ asset('assets/img/marker/hospital.png') }}",
			iconSize: iconSize,
			iconAnchor: [15,30]
		}), icon_F = L.icon({
			iconUrl: "{{ asset('assets/img/marker/firestation.png') }}",
			iconSize: iconSize,
			iconAnchor: [15,30]
		});

		var icon_E = L.icon({
			iconUrl: "{{ asset('assets/img/marker/emergency.png') }}",
			iconSize: iconSize,
			iconAnchor: [15,30]
		});

		var marker_fc = new Array(),
			marker_e = new Array();

		// set marker position
		<?php 
		$script = '';
		foreach ($markers as $marker) {
			$script .= 'marker_fc[' . $marker->gid . '] = ';
			$script .= 'L.marker([' . $marker->lat . ', ' . $marker->lng . '], {icon: icon_' . $marker->type . '})';
			$script .= '.bindPopup("<b>' . $marker->nama . '</b><br>Alamat : ' . $marker->alamat .'<br>Telpon : ' . $marker->telp . '<br>Koordinat : (' . $marker->lat . ', ' . $marker->lng . ')", {"offset": L.point(0,-20)})';
			$script .= '.addTo(map);';

			$script .= ' marker_fc[' . $marker->gid . '].on("mouseover", function(e){ this.openPopup(); })';
			$script .= '.on("mouseout", function(e){ this.closePopup(); })';
			$script .= '.on("click", function(e){ window.location = "' . action('FacilityController@getEdit', $marker->gid) . '" });';
			$script .= "\n";
		}

		echo $script;

		$script = '';
		foreach ($emergencies as $emergency) {
			$script .= 'marker_e[' . $emergency->case_id . '] = ';
			$script .= 'L.marker([' . $emergency->lat . ', ' . $emergency->lon . '], {icon: icon_E})';
			$script .= '.bindPopup("<b>Deskripsi : ' . $emergency->desc . '</b><br>Tipe : ' . $emergency->em_type->type_name .'<br>Pelapor : ' . $emergency->user_reporter->no_id . ' - ' . $emergency->user_reporter->nama . '<br>Validator : ' . $emergency->user_validator->no_id . ' - ' . $emergency->user_validator->nama . ($emergency->user_resolver ? '<br>Resolver : ' . $emergency->user_resolver->no_id . ' - ' . $emergency->user_resolver->nama : '') . '<br>Koordinat : (' . $emergency->lat . ', ' . $emergency->lon . ')", {"offset": L.point(0,-20)})';
			$script .= '.addTo(map);';

			$script .= ' marker_e[' . $emergency->case_id . '].on("mouseover", function(e){ this.openPopup(); })';
			$script .= '.on("mouseout", function(e){ this.closePopup(); })';
			$script .= '.on("click", function(e){ window.location = "' . action('EmergencyController@getEditEmergency', $emergency->case_id) . '" });';
			$script .= "\n";
		}

		echo $script;		
		?>

		// open popup
		@if(Input::has('cid'))
		if (marker_e{{ Input::get('cid') }}) {
			marker_e[{{ Input::get('cid') }}].openPopup();
		}
		@endif

		// pointer marker
		var icon_src = L.icon({
			iconUrl: "{{ asset('assets/img/marker/red_MarkerA.png') }}",
			iconAnchor: [10, 34] // posisikan koordinat ke bagian yang lancip dari icon
		}), icon_dest = L.icon({
			iconUrl: "{{ asset('assets/img/marker/yellow_MarkerB.png') }}",
			iconAnchor: [10, 34]
		});

		var src_marker = L.marker([0,0], { icon: icon_src, draggable: true }).addTo(map),
			dest_marker = L.marker([0,0], { icon: icon_dest, draggable: true }).addTo(map);

		// draggable
		src_marker.on('drag', function(e){

			position = this.getLatLng();

			$("[name='src_lat']").val(position.lat);
			$("[name='src_lng']").val(position.lng);

		});

		dest_marker.on('drag', function(e){

			position = this.getLatLng();

			$("[name='dest_lat']").val(position.lat);
			$("[name='dest_lng']").val(position.lng);

		});

		//////////////////////////////////////////////////////////////////////////////////////
		///
		//////////////////////////////////////////////////////////////////////////////////////

		//////////////////////////////////////////////////////////////////////////////////////
		// Context-Menu
		//////////////////////////////////////////////////////////////////////////////////////
		
		var Coordinates = markerPosition; // default coordinates

		roads.on("contextmenu", mapMenu);
		map.on("contextmenu", mapMenu);

		for (var key in marker_fc) {
			marker_fc[key].on("contextmenu", mapMenu);
		}
		for (var key in marker_e) {
			marker_e[key].on("contextmenu", mapMenu);
		}

		// context-menu // right click
		function mapMenu(e)
		{
			$("#map-context-menu").css({
				display: "block",
				left: e.originalEvent.pageX,
				top: e.originalEvent.pageY
			});

			Coordinates = [e.latlng.lat, e.latlng.lng];

			return false;
		}

		$("body").on("click", function(e){
			$("#map-context-menu").hide();
		});

		$("#map-src-trigger").on("click", srcFill);
		$("#map-dest-trigger").on("click", destFill);

		// fill src input field
		function srcFill(e) {
			e.preventDefault();

			src_marker.setLatLng(Coordinates);

			$("[name='src_lat']").val(Coordinates[0]);
			$("[name='src_lng']").val(Coordinates[1]);
		}
		// fill dest input field
		function destFill(e) {
			e.preventDefault();

			dest_marker.setLatLng(Coordinates);

			$("[name='dest_lat']").val(Coordinates[0]);
			$("[name='dest_lng']").val(Coordinates[1]);
		}

		//////////////////////////////////////////////////////////////////////////////////////
		///
		//////////////////////////////////////////////////////////////////////////////////////
		
		
		//////////////////////////////////////////////////////////////////////////////////////
		// Routing
		//////////////////////////////////////////////////////////////////////////////////////
		var routesFeatures = [], 
			routesStreets = {},
			routesRoads;

		var mxhr;
		
		$("#ng-route").on("click", function(e){

			e.preventDefault();

			var srcLat = $("[name='src_lat']").val(),
				srcLng = $("[name='src_lng']").val(),
				destLat = $("[name='dest_lat']").val(),
				destLng = $("[name='dest_lng']").val();

			if (!srcLat || !srcLng || !destLat || !destLng) {
				return false;
			}

			if (routesRoads) map.removeLayer(routesRoads);

			// draw route
			mxhr = $.ajax({
				url : "{{ action('HomeController@postAjaxRouting') }}",
				type : 'POST',
				dataType : 'json',
				data : {
					src_lat: srcLat,
					src_lng: srcLng,
					dest_lat: destLat,
					dest_lng: destLng
				},
				beforeSend: function() {
					$("#ng-route").attr("disabled", true).html("Loading");

					$('#panel-road').fadeIn('slow');

					$('#road-list').html('<img src="{{ asset('assets/img/preload.GIF') }}" style="display:block; margin:0 auto;">');
				},
				success : function(data) {
					$("#ng-route").attr("disabled", false).html("Route");
					
					routesFeatures = [];
					var li = '';

					// start path route
					for(var i = 0; i<data.length; i++) {
						var obj = {};

						obj.type = "Feature";
						obj.id = data[i].gid;
						obj.geometry = JSON.parse(data[i].geo_json);
						obj.properties = {
							name: data[i].street_name,
							direction: data[i].dir
						};

						routesFeatures.push(obj);

						if (i === 0 || i === (data.length-1)) {
							li += '<li><b>' + data[i].street_name + '</b></li>';	
						} else {
							li += '<li><small>' + data[i].street_name+ '</small></li>';
						}

					}

					routesStreets = {
						"type": "FeatureCollection",
						"features": routesFeatures
					};
					routesRoads = L.geoJson(routesStreets, {
						style: {
							color: "blue",
							weight: 5,
							opacity: 1
						}
					}).addTo(map);

					// generate road list
					var html = '<ul>' + li + '</ul>';
					$('#road-list').html(html);
				}, 
				error: function() {
					$("#ng-route").attr("disabled", false).html("Route");

					$('#panel-road').fadeOut('slow');

					alert("Maaf permintaan Anda tidak dapat kami penuhi saat ini.");
				}
			});

		});

		$("#ng-clear").on("click",function(e){
			e.preventDefault();

			if (mxhr) mxhr.abort();

			$('#panel-road').fadeOut('slow');

			$("[name='src_lat']").val('');
			$("[name='src_lng']").val('');
			$("[name='dest_lat']").val('');
			$("[name='dest_lng']").val('');

			map.removeLayer(routesRoads);
			routesRoads = null;

			src_marker.setLatLng([0,0]);
			dest_marker.setLatLng([0,0]);
		});

		//////////////////////////////////////////////////////////////////////////////////////
		///
		//////////////////////////////////////////////////////////////////////////////////////
		

		//////////////////////////////////////////////////////////////////////////////////////
		// Clock
		//////////////////////////////////////////////////////////////////////////////////////
		
		// update datetime
		function tUpdate()
		{
			moment.lang('id');
			var momentObj = moment(),
				timeNow = momentObj.format('HH:mm:ss'),
				dayNow = momentObj.format('dddd'),
				dateNow = momentObj.format('DD-MM-YYYY');

			$('#time-box').html(timeNow);
			$('#day-box').html(dayNow);
			$('#date-box').html(dateNow);
		}

		function updateTime()
		{
			tUpdate();
			setTimeout(updateTime, 1000);
		}

		updateTime();
		//////////////////////////////////////////////////////////////////////////////////////
		///
		//////////////////////////////////////////////////////////////////////////////////////
	});

	<?php 
	// set timezone to Indonesia
	date_default_timezone_set('Asia/Jakarta');
	?>

	// reload if meet next days
	var dayNow = moment("{{ date('d-m-Y H:i:s') }}", "DD-MM-YYYY HH:mm:ss"),
	 dayNext = moment("{{ date('d-m-Y', strtotime('+1 day')) . ' 00:00:00' }}", "DD-MM-YYYY HH:mm:ss"),
	 dayDiff = dayNext.diff(dayNow);

	setTimeout(function(){
		location.reload();
	}, dayDiff);
	</script>
@stop

@section('content')
<div class="container" id="content">
  
  <div class="row">
  	
  	<div class="col-md-2">

	    <div class="btn-group">
	      <a href="#" class="btn btn-default"><i class="glyphicon glyphicon-home"></i></a>
	      <a href="#" class="btn btn-primary active">Home</a>
	    </div>

	    <hr>

	    <div class="small-box bg-primary">
	    	<div class="inner">
	    		<h3 id="time-box">00:00:00</h3>
	    		<p id="date-box">01-01-1990</p>
	    	</div>
	    	<div class="icon">
	    		<i class="fa fa-clock-o"></i> 		
	    	</div>
	    	<div class="small-box-footer">
	    		<p id="day-box">Senin</p>
	    	</div>
	    </div>

	    <div>
	    	<p class="bg-default" style="padding: 2px 10px; border-bottom: 1px solid #ccc; text-align: center; font-weight: bold">Hari ini</p>
	    </div>

	    <div class="well">
	    	<p class="text-danger">Emergency: {{ $em_count->count }}</p>
	    	<p class="text-success">Selesai: {{ $em_success->count }}</p>
	    </div>

	    <div>
	    	@if(count($cases))
	    	<ul class="list-group">
	    		@foreach ($cases as $case)
	    			<li class="list-group-item">
	    				<span class="badge">{{ $case->count }}</span>
	    				{{ $case->type_name }}
	    			</li>
	    		@endforeach
	    	</ul>
	    	@endif
	    </div>

  	</div>

  	<div class="col-md-8">
  		<div id="map" class="map" style="height: 500px;"></div>
  	</div>

  	<div class="col-md-2">

  		<div class="panel panel-success">
  			<div class="panel-heading">
  				<h3 class="panel-title">Koordinat Asal</h3>
  			</div>

  			<div class="panel-body">
  				<div class="from-group">
  					{{ Form::text('src_lat', '', array(
  						'class' => 'form-control input-sm',
  						'placeholder' => 'Lintang',
  						'readonly' => 'readonly'
  					)) }}
  				</div>
  				<div style="margin: 10px 0;"></div>
  				<div class="from-group">
  					{{ Form::text('src_lng', '', array(
  						'class' => 'form-control input-sm',
  						'placeholder' => 'Bujur',
  						'readonly' => 'readonly'
  					)) }}
  				</div>
  			</div>
  		</div>	

  		<div class="panel panel-warning">
  			<div class="panel-heading">
  				<h3 class="panel-title">Koordinat Tujuan</h3>
  			</div>

  			<div class="panel-body">
  				<div class="from-group">
  					{{ Form::text('dest_lat', '', array(
  						'class' => 'form-control input-sm',
  						'placeholder' => 'Lintang',
  						'readonly' => 'readonly'
  					)) }}
  				</div>
  				<div style="margin: 10px 0;"></div>
  				<div class="from-group">
  					{{ Form::text('dest_lng', '', array(
  						'class' => 'form-control input-sm',
  						'placeholder' => 'Bujur',
  						'readonly' => 'readonly'
  					)) }}
  				</div>
  			</div>
  		</div>

  		<button type="button" class="btn btn-primary btn-block" id="ng-route">Route</button>
  		<button type="button" class="btn btn-danger btn-block" id="ng-clear">Clear</button>

  		<div class="panel panel-default" id="panel-road" style="display: none">
  			<div class="panel-body" id="road-list"></div>
  		</div>

  	</div>
	</div>

</div>

<div id="map-context-menu" class="dropdown clearfix">
	<ul class="dropdown-menu list-context-menu" role="menu">
		<li><a href="#" id="map-src-trigger">Koordinat Asal</a></li>
		<li class="divider"></li>
		<li><a href="#" id="map-dest-trigger">Koordinat Tujuan</a></li>
	</ul>
</div>

@stop