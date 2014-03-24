@extends('layouts.cpanel')
@section('title','Edit Emergency | ' . $em_case->type)
@section('body_class','cpanel edit-em')

@section('bottom_css')
	@parent
	<link rel="stylesheet" href="{{ asset('assets/css/cpanel.css') }}">	
	<link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
	<link rel="stylesheet" href="{{ asset('packages/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}">	
	<link rel="stylesheet" href="{{ asset('packages/leaflet/leaflet.css') }}">
@stop

@section('bottom_js')
	@parent
	<script src="{{ asset('packages/moment/moment.min.js') }}"></script>
	<script src="{{ asset('packages/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
	<script src="{{ asset('packages/leaflet/leaflet.js'); }}"></script>
	<script>
	jQuery(function($){

		$('#datetimepicker').datetimepicker({
			sideBySide: true,
			useSeconds: true
		});

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
		@if(Input::old('lat') && Input::old('lon'))
			var markerPosition = [{{ Input::old('lat') }}, {{ Input::old('lon') }}];
		@elseif(!$em_case->lat || !$em_case->lon)
			var markerPosition = map.getCenter();
		@else
			var markerPosition = [{{ $em_case->lat }}, {{ $em_case->lon }}];
		@endif

		map.setView(markerPosition, 13);

		// draw marker
		var marker = L.marker(markerPosition).addTo(map);

		// draw the road
		var roads = L.geoJson(streets,{
			style: {
				color: "orange",
				weight: 3,
				opacity: 1
			}
		}).addTo(map);

		// map onclick
		function mapClick(e){
			// update marker
			marker.setLatLng(e.latlng);
			// populate form
			$('#lat').val(e.latlng.lat);
			$('#lon').val(e.latlng.lng);
		}

		map.on('click', mapClick);
		roads.on('click', mapClick);

	});
	</script>
@stop

@section('content')
<div class="container" id="content">
  
  <div class="row">
  	<div class="col-md-12">
	    <div class="btn-group">
	      <a href="#" class="btn btn-default"><i class="glyphicon glyphicon-home"></i></a>
	      <a href="{{ action('EmergencyController@getIndex') }}" class="btn btn-default">Emergency</a>
	      <a href="{{ action('EmergencyController@getIndexEmergency') }}" class="btn btn-default">Kasus</a>
	      <a href="#" class="btn btn-primary active">Edit</a>
	    </div>
	    <hr>
  	</div>
	</div>

	{{ Form::model($em_case, array(
		'action' => array('EmergencyController@putEditEmergency', $em_case->case_id),
		'class' => 'form-horizontal',
		'method' => 'put'
	)) }}
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-body">

					
					<div class="row">
						<div class="col-md-5">

							<div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
								{{ Form::label('type', 'Tipe', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::select('type', $em_types, null, array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('type') ? 'tooltip' : '',
										'data-title' => $errors->has('type') ? $errors->first('type') : ''
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

							<div class="form-group {{ $errors->has('lon') ? 'has-error' : '' }}">
								{{ Form::label('lon', 'Longitude', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::text('lon', null, array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('lon') ? 'tooltip' : '',
										'data-title' => $errors->has('lon') ? $errors->first('lon') : '',
										'placeholder' => 'Koordinat Bujur',
										'readonly' => 'readonly'
									)) }}
								</div>
							</div>

							<div class="form-group {{ $errors->has('time') ? 'has-error' : '' }}">
								{{ Form::label('time', 'Waktu Kejadian', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::text('time', Input::old('time', date('d-m-Y H:i:s', strtotime($em_case->time))), array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('time') ? 'tooltip' : '',
										'data-title' => $errors->has('time') ? $errors->first('time') : '',
										'placeholder' => 'Pilih Waktu Kejadian',
										'data-date-format' => 'DD-MM-YYYY HH:mm:ss',
										'id' => 'datetimepicker'
									)) }}
								</div>
							</div>

							<?php // determine color
							$opt_color = array(
								5 => 'bg-default',
								4 => 'bg-warning',
								3 => 'bg-danger',
								2 => 'bg-success',
								1 => 'bg-info'
							);
							?>

							<div class="form-group {{ $errors->has('reporter') ? 'has-error' : '' }}">
								{{ Form::label('reporter', 'Pelapor', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									<select name="reporter" id="reporter" class="form-control has-tooltip" data-toggle="{{ $errors->has('reporter') ? 'tooltip' : '' }}" data-title="{{ $errors->has('reporter') ? $errors->first('reporter') : '' }}">
										<option value="">Pilih Pelapor</option>
										@foreach ($users_data as $usd_key => $user_data)
											<optgroup label="{{ $group_name[$usd_key] }}">
												@foreach ($user_data as $user)
													<?php 
													$value = Input::old('reporter', $em_case->reporter);
													$selected = ($value == $user->no_id) ? ' selected="selected"' : '';
													?>
													<option value="{{ $user->no_id }}" {{ $selected }} class="{{ $opt_color[$usd_key] }}">{{ $user->no_id . ' - ' . $user->nama }}</option>
												@endforeach
											</optgroup>
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group {{ $errors->has('validator') ? 'has-error' : '' }}">
								{{ Form::label('validator', 'Validator', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									<select name="validator" id="validator" class="form-control has-tooltip" data-toggle="{{ $errors->has('validator') ? 'tooltip' : '' }}" data-title="{{ $errors->has('validator') ? $errors->first('validator') : '' }}">
										<option value="">Pilih Validator</option>
										@foreach ($users_data as $usd_key => $user_data)
											<?php if ($usd_key == 4) continue; ?> {{-- no sipil --}}
											<optgroup label="{{ $group_name[$usd_key] }}">
												@foreach ($user_data as $user)
													<?php 
													$value = Input::old('validator', $em_case->validator);
													$selected = ($value == $user->no_id) ? ' selected="selected"' : '';
													?>
													<option value="{{ $user->no_id }}" {{ $selected }} class="{{ $opt_color[$usd_key] }}">{{ $user->no_id . ' - ' . $user->nama }}</option>
												@endforeach
											</optgroup>
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
								{{ Form::label('status', 'Status', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::select('status', $status, null, array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('status') ? 'tooltip' : '',
										'data-title' => $errors->has('status') ? $errors->first('status') : '',
									)) }}
								</div>
							</div>

							<div class="form-group {{ $errors->has('desc') ? 'has-error' : '' }}">
								{{ Form::label('desc', 'Deskripsi', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::textarea('desc', null, array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('desc') ? 'tooltip' : '',
										'data-title' => $errors->has('desc') ? $errors->first('desc') : '',
										'placeholder' => 'Ketikkan Deskripsi Emergency',
										'rows' => '3'
									)) }}
								</div>
							</div>

						</div>

						<div class="col-md-7">
							<div id="map" class="map" style="height: 420px"></div>
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
				  'onclick' => 'window.location="'. action('EmergencyController@getIndexEmergency') .'"'
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