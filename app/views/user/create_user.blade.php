@extends('layouts.cpanel')
@section('title','Tambah User Umum')
@section('body_class','cpanel create-user')

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
			if ($(this).val() !== '') {
				// destroy tooltip
				$(this).tooltip('destroy');
				// remove has-error
				$(this).parents('.form-group').removeClass('has-error');
			}
		}).tooltip({
			placement: 'top',
			trigger: 'focus'
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
	      <a href="#" class="btn btn-default">User</a>
	      <a href="#" class="btn btn-default">Umum</a>
	      <a href="#" class="btn btn-primary active">Tambah</a>
	    </div>
	    <hr>
  	</div>
	</div>

	{{ Form::open(array(
		'action' => 'UserController@postCreateUser',
		'class' => 'form-horizontal'
	)) }}
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-body">

					
					<div class="row">
						<div class="col-md-6">
							
							<div class="form-group {{ $errors->has('no_id') ? 'has-error' : '' }}">
								{{ Form::label('no_id', 'No ID', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::text('no_id', Input::old('no_id', ''), array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('no_id') ? 'tooltip' : '',
										'data-title' => $errors->has('no_id') ? $errors->first('no_id') : '',
										'placeholder' => 'Ketikkan No ID Anda',
										'maxlength' => '16'
									)) }}
								</div>
							</div>

							<div class="form-group {{ $errors->has('nama') ? 'has-error' : '' }}">
								{{ Form::label('nama', 'Nama', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::text('nama', Input::old('nama', ''), array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('nama') ? 'tooltip' : '',
										'data-title' => $errors->has('nama') ? $errors->first('nama') : '',
										'placeholder' => 'Ketikkan Nama Anda'
									)) }}
								</div>
							</div>

							<div class="form-group {{ $errors->has('pass') ? 'has-error' : '' }}">
								{{ Form::label('pass', 'Password', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::password('pass', array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('pass') ? 'tooltip' : '',
										'data-title' => $errors->has('pass') ? $errors->first('pass') : '',
										'placeholder' => 'Ketikkan Password Anda'
									)) }}
								</div>
							</div>

							<div class="form-group {{ $errors->has('pass_confirmation') ? 'has-error' : '' }}">
								{{ Form::label('pass_confirmation', 'Konfirmasi Password', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::password('pass_confirmation', array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('pass_confirmation') ? 'tooltip' : '',
										'data-title' => $errors->has('pass_confirmation') ? $errors->first('pass_confirmation') : '',
										'placeholder' => 'Ketikkan Konfirmasi Password Anda'
									)) }}
								</div>
							</div>

							<div class="form-group {{ $errors->has('tmp_lhr') ? 'has-error' : '' }}">
								{{ Form::label('tmp_lhr', 'Tempat Lahir', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::text('tmp_lhr', Input::old('tmp_lhr', ''), array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('tmp_lhr') ? 'tooltip' : '',
										'data-title' => $errors->has('tmp_lhr') ? $errors->first('tmp_lhr') : '',
										'placeholder' => 'Ketikkan Tempat Lahir Anda'
									)) }}
								</div>
							</div>

							<div class="form-group {{ $errors->has('tgl_lhr') ? 'has-error' : '' }}">
								{{ Form::label('tgl_lhr', 'Tanggal Lahir', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::text('tgl_lhr', Input::old('tgl_lhr', ''), array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('tgl_lhr') ? 'tooltip' : '',
										'data-title' => $errors->has('tgl_lhr') ? $errors->first('tgl_lhr') : '',
										'placeholder' => 'Pilih Tanggal Lahir Anda',
										'data-date-format' => 'DD-MM-YYYY',
										'id' => 'datetimepicker'
									)) }}
								</div>
							</div>

							<div class="form-group {{ $errors->has('gender') ? 'has-error' : '' }}">
								{{ Form::label('gender', 'Jenis Kelamin', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::select('gender', $gender, Input::old('gender', ''), array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('gender') ? 'tooltip' : '',
										'data-title' => $errors->has('gender') ? $errors->first('gender') : ''
									)) }}
								</div>
							</div>

						</div>

						<div class="col-md-6">
							
							<div class="form-group {{ $errors->has('alamat') ? 'has-error' : '' }}">
								{{ Form::label('alamat', 'Alamat', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::textarea('alamat', Input::old('alamat', ''), array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('alamat') ? 'tooltip' : '',
										'data-title' => $errors->has('alamat') ? $errors->first('alamat') : '',
										'placeholder' => 'Ketikkan Alamat Anda',
										'rows' => '7'
									)) }}
								</div>
							</div>

							<div class="form-group {{ $errors->has('pekerjaan') ? 'has-error' : '' }}">
								{{ Form::label('pekerjaan', 'Pekerjaan', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::text('pekerjaan', Input::old('pekerjaan', ''), array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('pekerjaan') ? 'tooltip' : '',
										'data-title' => $errors->has('pekerjaan') ? $errors->first('pekerjaan') : '',
										'placeholder' => 'Ketikkan Pekerjaan Anda'
									)) }}
								</div>
							</div>

							<div class="form-group {{ $errors->has('no_hp') ? 'has-error' : '' }}">
								{{ Form::label('no_hp', 'No HP', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::text('no_hp', Input::old('no_hp', ''), array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('no_hp') ? 'tooltip' : '',
										'data-title' => $errors->has('no_hp') ? $errors->first('no_hp') : '',
										'placeholder' => 'Ketikkan No Handphone Anda'
									)) }}
								</div>
							</div>

							<div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
								{{ Form::label('email', 'Email', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::text('email', Input::old('email', ''), array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('email') ? 'tooltip' : '',
										'data-title' => $errors->has('email') ? $errors->first('email') : '',
										'placeholder' => 'Ketikkan Alamat Email Anda'
									)) }}
								</div>
							</div>

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
				  'onclick' => 'window.location="'. action('UserController@getIndexUser') .'"'
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