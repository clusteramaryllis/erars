@extends('layouts.cpanel')
@section('title','Indeks Statistik Emergency')
@section('body_class','cpanel index-em-stats')

@section('bottom_css')
	@parent
	<link rel="stylesheet" href="{{ asset('assets/css/cpanel.css') }}">	
	<link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
	<link rel="stylesheet" href="{{ asset('packages/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}">	
@stop

@section('bottom_js')
	@parent
	<script src="{{ asset('packages/moment/moment.min.js') }}"></script>
	<script src="{{ asset('packages/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
	<script>
	jQuery(function($){

		// date time picker
		$('#date_from').datetimepicker({
			pickTime: false
		}).on('blur', function(){
			var val = $(this).val();

			setMinDate(val);
		});

		$('#date_to').datetimepicker({
			pickTime: false
		});

		// init
		if ($('#date_from') !== '') {
			setMinDate($('#date_from').val());
		}

		function setMinDate(val) {
			var dateData = val.split('-'),
			 dateString = dateData[1] + '/' + dateData[0] + '/' + dateData[2]; // convert DD-MM-YYYY to MM/DD/YYYY

			$('#date_to').data('DateTimePicker').setMinDate(new Date(dateString));
		}

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
	      <a href="{{ action('HomeController@getIndex') }}" class="btn btn-default"><i class="glyphicon glyphicon-home"></i></a>
	      <a href="{{ action('EmergencyController@getIndex') }}" class="btn btn-default">Emergency</a>
	      <a href="#" class="btn btn-primary active">Statistik</a>
	    </div>
	    <hr>
  	</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="well well-sm clearfix">
				{{ Form::open(array(
					'class' => 'form-inline form-inline-wide',
					'method' => 'get'
				)) }}

				<div class="form-group">
					{{ Form::label('filter_by', 'Filter Berdasarkan') }}
					{{ Form::select('filter_by', $filterBy, Input::has('filter_by') ? Input::get('filter_by') : '', array(
						'class' => 'form-control'
					)) }}
				</div>

				<div class="form-group">
					{{ Form::label('from', 'From') }}
					{{ Form::text('from', Input::has('from') ? Input::get('from') : '', array(
						'class' => 'form-control',
						'data-date-format' => 'DD-MM-YYYY',
						'id' => 'date_from'
					)) }}
				</div>

				<div class="form-group">
					{{ Form::label('to', 'To') }}
					{{ Form::text('to', Input::has('to') ? Input::get('to') : '', array(
						'class' => 'form-control',
						'data-date-format' => 'DD-MM-YYYY',
						'id' => 'date_to'
					)) }}
				</div>

				{{ Form::button('<i class="fa fa-search"></i>', array(
					'type' => 'submit',
					'class' => 'btn btn-primary'
				)) }}

				{{ Form::close() }}
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div style="margin: 0 0 15px">
				<ul class="nav nav-tabs">
	    		<li class="active"><a href="#">Tabel</a></li>
	    		<li><a href="{{ action('EmergencyController@getChartStatistic') . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '') }}">Chart</a></li>
	    	</ul>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			@if(count($em_cases))
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Tanggal</th>
							<th>Total Kejadian</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($em_cases as $em_case)
						<tr>
							<td>{{ date('d-m-Y', strtotime($em_case->date)) }}</td>
							<td>{{ $em_case->count }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			@else
			<p class="centered">Data Kosong</p>
			@endif
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="centered">
				{{ $em_cases->links() }}
			</div>
		</div>
	</div>

</div>
@stop