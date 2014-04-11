@extends('layouts.cpanel')
@section('title','Simulasi')
@section('body_class','cpanel simulation')

@section('bottom_css')
	@parent
	<link rel="stylesheet" href="{{ asset('assets/css/cpanel.css') }}">	
	<link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
	<link rel="stylesheet" href="{{ asset('packages/leaflet/leaflet.css') }}">
@stop

@section('bottom_js')
	@parent
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
				url : "{{ action('HomeController@postAjaxSimulation') }}",
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

					$('#simulation-panel').html('<div class="progress progress-striped active" style="margin-top: 10px"><div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%">Fetch Data</div></div>');
					$('#map2-panel').html('');
				},
				success : function(data) {
					$("#ng-route").attr("disabled", false).html("Route");

					var html = '';
					html += '<div class="table-responsive">';
					html += '<table class="table table-striped">';
					html += '<thead>';
					html += '<tr><th width="8%">PP #</th><th>Jalur</th><th>Jarak Tempuh</th></tr>';
					html += '</thead>';
					html += '<tbody>';
					for (var i=0, len = data['population'].length; i<len; i++) {
						html += '<tr><td>' + (i+1) + '</td><td>' + data['population'][i]['path'].join(' > ') + '</td><td>' + data['population'][i]['cost'] + '</td></tr>';
					}
					html += '</tbody>';
					html += '</table>';
					html += '</div><hr>';
					
					for (var i=0, len = data['offspring'].length; i<len; i++) {
						html += '<b>Generasi ke - ' + (i+1) + '</b>';
						html += '<div class="table-responsive">';
						html += '<table class="table table-striped">';
						html += '<thead>';
						html += '<tr><th width="8%">PP #</th><th>Crossover</th><th>Jalur</th><th>Jarak Tempuh</th></tr>';
						html += '</thead>';
						html += '<tbody>';
						for (var j = 0, jLen = data['offspring'][i].length; j<jLen; j++) {
							html += '<tr><td>' + (j+1) + '</td><td>' +  data['offspring'][i][j]['parent1'] + ' &gt;&lt; ' + data['offspring'][i][j]['parent2'] + '</td><td>' +  data['offspring'][i][j]['path'].join(' > ') + '</td><td>' +  data['offspring'][i][j]['cost'] + '</td></tr>';
						}
						html += '</tbody>';
						html += '</table>';
						html += '</div>';
					}

					html += '<p><b>Jalur Terbaik :</b> ' + data['bestpath']['path'].join(' > ') + ' (Jarak Tempuh : ' + data['bestpath']['cost'] + ')</p>';

					html2 = '<div id="map2" class="map2" style="height:200px"></div>';

					$('#simulation-panel').html(html);
					$('#map2-panel').html(html2);

					var map2 = L.map('map2', {
						center: [-6.98250, 110.43011],
						zoom: 13,
						maxZoom: 16,
						minZoom:13
					});

					// set marker position

					map2.setView(map.getCenter(), map.getZoom());

					// draw the road
					var roads2 = L.geoJson(streets,{
						style: {
							color: "orange",
							weight: 3,
							opacity: 1
						}
					}).addTo(map2);

					var smp = src_marker.getLatLng(),
						dmp = dest_marker.getLatLng();

					var src_marker2 = L.marker([smp.lat, smp.lng], { icon: icon_src }).addTo(map2),
						dest_marker2 = L.marker([dmp.lat, dmp.lng], { icon: icon_dest }).addTo(map2);
					
					routesFeatures = [];

					// start path route
					for(var i = 0; i<data['geojson'].length; i++) {
						var obj = {};

						obj.type = "Feature";
						obj.id = data['geojson'][i].gid;
						obj.geometry = JSON.parse(data['geojson'][i].geo_json);
						obj.properties = {
							name: data['geojson'][i].street_name,
							direction: data['geojson'][i].dir
						};

						routesFeatures.push(obj);
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
					}).addTo(map2);

				}, 
				error: function() {
					$("#ng-route").attr("disabled", false).html("Route");

					$('#simulation-panel').html('');
					$('#map2-panel').html('');

					alert("Maaf permintaan Anda tidak dapat kami penuhi saat ini.");
				}
			});

		});

		//////////////////////////////////////////////////////////////////////////////////////
		///
		//////////////////////////////////////////////////////////////////////////////////////
		
	});
	</script>
@stop

@section('content')

<div class="container" id="content">
  
  <div class="row">
  	<div class="col-md-12">
	    <div class="btn-group">
	      <a href="{{ action('HomeController@getIndex') }}" class="btn btn-default"><i class="glyphicon glyphicon-home"></i></a>
	      <a href="#" class="btn btn-primary active">Simulasi</a>
	    </div>
	    <hr>
  	</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-body">
					
					<div class="row">
						<div class="col-md-7">
							<div id="map" class="map" style="height: 200px;"></div>
						</div>

						<div class="col-md-5">
							
							{{ Form::open(array(
								'url' => '#',
								'class' => 'form-horizontal'
							)) }}

							<div class="form-group">
								{{ Form::label('src_coordinate', 'Koordinat Asal', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-4">
									{{ Form::text('src_lat', null, array(
										'class' => 'form-control',
										'placeholder' => 'Lintang',
										'readonly' => 'readonly'
									)) }}
								</div>
								<div class="col-md-4">
									{{ Form::text('src_lng', null, array(
										'class' => 'form-control',
										'placeholder' => 'Bujur',
										'readonly' => 'readonly'
									)) }}
								</div>
							</div>

							<div class="form-group">
								{{ Form::label('dest_coordinate', 'Koordinat Tujuan', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-4">
									{{ Form::text('dest_lat', null, array(
										'class' => 'form-control',
										'placeholder' => 'Lintang',
										'readonly' => 'readonly'
									)) }}
								</div>
								<div class="col-md-4">
									{{ Form::text('dest_lng', null, array(
										'class' => 'form-control',
										'placeholder' => 'Bujur',
										'readonly' => 'readonly'
									)) }}
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-offset-4 col-md-4">
									{{ Form::button('Route', array(
										'id' => 'ng-route',
										'class' => 'btn btn-success'
									)) }}
								</div>
							</div>

							{{ Form::close() }}

						</div>
					</div>

					<div class="row">
						<div class="col-md-12" id="simulation-panel">

						</div>
					</div>

					<div class="row">
						<div class="col-md-7" id="map2-panel">

						</div>
					</div>

				</div>
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