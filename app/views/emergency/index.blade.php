@extends('layouts.cpanel')
@section('title','Indeks Emergency')
@section('body_class','cpanel indeks-all')

@section('bottom_css')
	@parent
	<link rel="stylesheet" href="{{ asset('assets/css/cpanel.css') }}">	
	<link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
@stop

@section('bottom_js')
	@parent
	<script>
	jQuery(function($){

		// disabled hash reference
		$('[href="#"]').on('click', function(e){
			e.preventDefault();
		});

	});
	</script>
@stop

@section('content')
<div class="container" id="content">

  <div class="row">
  	<div class="col-md-12 clearfix">
	    <div class="btn-group">
	      <a href="#" class="btn btn-default"><i class="glyphicon glyphicon-home"></i></a>
	      <a href="#" class="btn btn-primary active">Emergency</a>
	    </div>
	    <hr>
  	</div>
	</div>
	
	<div class="row">
		
		<div class="col-md-4">
			<div class="thumbnail">
				<a href="{{ action('EmergencyController@getIndexEmergency') }}" class="icon-panel">
					<i class="fa fa-warning"></i>
				</a>
				<div class="caption">
					<h3>
						<a href="{{ action('EmergencyController@getIndexEmergency') }}" class="same">
							Kasus
						</a>
					</h3>
				</div>
			</div>
		</div>

		<div class="col-md-4">
			<div class="thumbnail">
				<a href="{{ action('EmergencyController@getIndexType') }}" class="icon-panel">
					<i class="fa fa-th-list"></i>
				</a>
				<div class="caption">
					<h3>
						<a href="{{ action('EmergencyController@getIndexType') }}" class="same">
							Tipe Emergency
						</a>
					</h3>
				</div>
			</div>
		</div>

		<div class="col-md-4">
			<div class="thumbnail">
				<a href="{{ action('EmergencyController@getIndexStatistic') }}" class="icon-panel">
					<i class="fa fa-bar-chart-o"></i>
				</a>
				<div class="caption">
					<h3>
						<a href="{{ action('EmergencyController@getIndexStatistic') }}" class="same">
							Statistik
						</a>
					</h3>
				</div>
			</div>
		</div>

	</div>
</div>
@stop