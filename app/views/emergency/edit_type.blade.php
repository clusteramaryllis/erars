@extends('layouts.cpanel')
@section('title','Edit Tipe Emergency | ' . $em_type->type_name)
@section('body_class','cpanel edit-em-type')

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
		$('#datetimepicker').datetimepicker({
			pickTime: false
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
		
	});
	</script>
@stop

@section('content')
<div class="container" id="content">
  
  <div class="row">
  	<div class="col-md-12">
	    <div class="btn-group">
	      <a href="{{ action('HomeController@getIndex') }}" class="btn btn-default"><i class="glyphicon glyphicon-home"></i></a>
	      <a href="{{ action('EmergencyController@getIndex') }}" class="btn btn-default">Emergency</a>
	      <a href="{{ action('EmergencyController@getIndexType') }}" class="btn btn-default">Tipe Emergency</a>
	      <a href="#" class="btn btn-primary active">Edit</a>
	    </div>
	    <hr>
  	</div>
	</div>

	{{ Form::model($em_type, array(
		'action' => array('EmergencyController@putEditType', $em_type->type_id),
		'class' => 'form-horizontal',
		'method' => 'put'
	)) }}
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-body">

					
					<div class="row">
						<div class="col-md-6">

							<div class="form-group {{ $errors->has('type_name') ? 'has-error' : '' }}">
								{{ Form::label('type_name', 'Nama Tipe', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::text('type_name', null, array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('type_name') ? 'tooltip' : '',
										'data-title' => $errors->has('type_name') ? $errors->first('type_name') : '',
										'placeholder' => 'Ketikkan Nama Tipe Emergency'
									)) }}
								</div>
							</div>

							<div class="form-group">
								{{ Form::label('checkbox', 'Peringatkan', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									@foreach ($groupType as $key => $gt)
									<div class="checkbox">
										<label>{{ Form::checkbox($key, '1', null) }} {{ $gt }}</label>
									</div>	
									@endforeach
								</div>
							</div>							

						</div>

						<div class="col-md-6"></div>

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
				  'onclick' => 'window.location="'. action('EmergencyController@getIndexType') .'"'
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