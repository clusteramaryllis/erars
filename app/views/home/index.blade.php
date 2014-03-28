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

		// map init
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
			iconSize: iconSize
		}), icon_M = L.icon({
			iconUrl: "{{ asset('assets/img/marker/hospital.png') }}",
			iconSize: iconSize
		}), icon_F = L.icon({
			iconUrl: "{{ asset('assets/img/marker/firestation.png') }}",
			iconSize: iconSize
		});

		var icon_E = L.icon({
			iconUrl: "{{ asset('assets/img/marker/emergency.png') }}",
			iconSize: iconSize
		});

		// set marker position
		<?php 
		$script = '';
		foreach ($markers as $marker) {
			$script .= 'var marker_fc' . $marker->gid . ' = ';
			$script .= 'L.marker([' . $marker->lat . ', ' . $marker->lng . '], {icon: icon_' . $marker->type . '})';
			$script .= '.bindPopup("<b>' . $marker->nama . '</b><br>Alamat : ' . $marker->alamat .'<br>Telpon : ' . $marker->telp . '<br>Koordinat : (' . $marker->lat . ', ' . $marker->lng . ')", {"offset": L.point(0,-20)})';
			$script .= '.addTo(map);';

			$script .= 'marker_fc' . $marker->gid . '.on("mouseover", function(e){ this.openPopup(); })';
			$script .= '.on("mouseout", function(e){ this.closePopup(); })';
			$script .= '.on("click", function(e){ window.location = "' . action('FacilityController@getEdit', $marker->gid) . '" })';
			$script .= "\n";
		}

		echo $script;

		$script = '';
		foreach ($emergencies as $emergency) {
			$script .= 'var marker_e' . $emergency->case_id . ' = ';
			$script .= 'L.marker([' . $emergency->lat . ', ' . $emergency->lon . '], {icon: icon_E})';
			$script .= '.bindPopup("<b>Deskripsi : ' . $emergency->desc . '</b><br>Tipe : ' . $emergency->em_type->type_name .'<br>Pelapor : ' . $emergency->user_reporter->no_id . ' - ' . $emergency->user_reporter->nama . '<br>Validator : ' . $emergency->user_validator->no_id . ' - ' . $emergency->user_validator->nama . '<br>Resolver : ' . $emergency->user_resolver->no_id . ' - ' . $emergency->user_resolver->nama . '<br>Koordinat : (' . $emergency->lat . ', ' . $emergency->lon . ')", {"offset": L.point(0,-20)})';
			$script .= '.addTo(map);';

			$script .= 'marker_e' . $emergency->case_id . '.on("mouseover", function(e){ this.openPopup(); })';
			$script .= '.on("mouseout", function(e){ this.closePopup(); })';
			$script .= '.on("click", function(e){ window.location = "' . action('EmergencyController@getEditEmergency', $emergency->case_id) . '" })';
			$script .= "\n";
		}

		echo $script;		
		?>

		// open popup
		@if(Input::has('cid'))
		if (marker_e{{ Input::get('cid') }}) {
			marker_e{{ Input::get('cid') }}.openPopup();
		}
		@endif

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

  	<div class="col-md-10">
  		<div id="map" class="map" style="height: 500px;"></div>
  	</div>
	</div>

</div>
@stop