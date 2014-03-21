@extends('layouts.cpanel')
@section('title','Edit Fasilitas | '. $facility->nama)
@section('body_class','cpanel edit-facility')

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

		$('.has-tooltip').on('keypress', function(){
			removeTooltip($(this));
		}).on('change', function(){
			removeTooltip($(this));
		}).tooltip({
			placement: 'top',
			trigger: 'focus'
		});

		function removeTooltip(elm) {
			if (elm.val() !== '') {
				// destroy tooltip
				elm.tooltip('destroy');
				// remove has-error
				elm.parents('.form-group').removeClass('has-error');
			}
		}

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
		@if(Input::old('lat') && Input::old('lng'))
			var markerPosition = [{{ Input::old('lat') }}, {{ Input::old('lng') }}];
		@elseif(!$facility->lat || !$facility->lng)
			var markerPosition = map.getCenter();
		@else
			var markerPosition = [{{ $facility->lat }}, {{ $facility->lng }}];
		@endif

		map.setView(markerPosition, 13);

		// draw marker
		var marker = L.marker(markerPosition).addTo(map);

		// draw the road
		L.geoJson(streets,{
			style: {
				color: "orange",
				weight: 3,
				opacity: 1
			}
		}).addTo(map);

		// map onclick
		map.on('click', function(e){
			// update marker
			marker.setLatLng(e.latlng);
			// populate form
			$('#lat').val(e.latlng.lat);
			$('#lng').val(e.latlng.lng);
		});
	});
	</script>
@stop

@section('content')
<div class="container" id="content">
  
  <div class="row">
  	<div class="col-md-12">
	    <div class="btn-group">
	      <a href="#" class="btn btn-default"><i class="glyphicon glyphicon-home"></i></a>
	      <a href="{{ action('FacilityController@getIndex') }}" class="btn btn-default">Fasilitas</a>
	      <a href="#" class="btn btn-primary active">Edit</a>
	    </div>
	    <hr>
  	</div>
	</div>

	{{ Form::model($facility, array(
		'action' => array('FacilityController@putEdit', $facility->gid),
		'class' => 'form-horizontal',
		'method' => 'put'
	)) }}
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-body">

					
					<div class="row">
						<div class="col-md-5">

							<div class="form-group {{ $errors->has('nama') ? 'has-error' : '' }}">
								{{ Form::label('nama', 'Nama', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::text('nama', null, array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('nama') ? 'tooltip' : '',
										'data-title' => $errors->has('nama') ? $errors->first('nama') : '',
										'placeholder' => 'Ketikkan Nama Fasilitas'
									)) }}
								</div>
							</div>

							<div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
								{{ Form::label('type', 'Tipe', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::select('type', $type, null, array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('type') ? 'tooltip' : '',
										'data-title' => $errors->has('type') ? $errors->first('type') : ''
									)) }}
								</div>
							</div>

							<div class="form-group {{ $errors->has('alamat') ? 'has-error' : '' }}">
								{{ Form::label('alamat', 'Alamat', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::textarea('alamat', null, array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('alamat') ? 'tooltip' : '',
										'data-title' => $errors->has('alamat') ? $errors->first('alamat') : '',
										'placeholder' => 'Ketikkan Alamat Fasilitas',
										'rows' => '3'
									)) }}
								</div>
							</div>

							<div class="form-group {{ $errors->has('telp') ? 'has-error' : '' }}">
								{{ Form::label('telp', 'Telpon', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::text('telp', null, array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('telp') ? 'tooltip' : '',
										'data-title' => $errors->has('telp') ? $errors->first('telp') : '',
										'placeholder' => 'Ketikkan No Telpon Fasilitas'
									)) }}
								</div>
							</div>

							<div class="form-group {{ $errors->has('lat') ? 'has-error' : '' }}">
								{{ Form::label('lat', 'Latitude', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::text('lat', null, array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('lat') ? 'tooltip' : '',
										'data-title' => $errors->has('lat') ? $errors->first('lat') : '',
										'placeholder' => 'Koordinat Lintang',
										'readonly' => 'readonly'
									)) }}
								</div>
							</div>

							<div class="form-group {{ $errors->has('lng') ? 'has-error' : '' }}">
								{{ Form::label('lng', 'Longitude', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::text('lng', null, array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('lng') ? 'tooltip' : '',
										'data-title' => $errors->has('lng') ? $errors->first('lng') : '',
										'placeholder' => 'Koordinat Bujur',
										'readonly' => 'readonly'
									)) }}
								</div>
							</div>

						</div>

						<div class="col-md-7">
							<div id="map" class="map" style="height: 340px"></div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="well well-sm clearfix">
				{{ Form::button('Batal', array(
				  'type' => 'button', 
				  'class' => 'btn btn-default pull-right', 
				  'onclick' => 'window.location="'. action('FacilityController@getIndex') .'"'
				 )) }}
				{{ Form::button('Simpan', array(
				  'type' => 'submit', 
				  'class' => 'btn btn-success pull-right',
				  'style' => 'margin-right: 10px'
				)) }}
			</div>
		</div>
	</div>
	{{ Form::close() }}

</div>
@stop