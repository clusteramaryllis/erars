@extends('layouts.cpanel')
@section('title','Edit User Umum | ' . $user->nama)
@section('body_class','cpanel edit-user')

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
			pickTime: false,
			maxDate: new Date('{{ $maxDate }}')
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
	      <a href="{{ action('UserController@getIndex') }}" class="btn btn-default">User</a>
	      <a href="{{ action('UserController@getIndexUser') }}" class="btn btn-default">Umum</a>
	      <a href="#" class="btn btn-primary active">Edit</a>
	    </div>
	    <hr>
  	</div>
	</div>

	{{ Form::model($user, array(
		'action' => array('UserController@putEditUser', $user->no_id),
		'class' => 'form-horizontal',
		'method' => 'put'
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
									{{ Form::text('no_id', null, array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('no_id') ? 'tooltip' : '',
										'data-title' => $errors->has('no_id') ? $errors->first('no_id') : '',
										'placeholder' => 'Ketikkan No ID Anda',
										'maxlength' => '16',
										'disabled' => 'disabled',
										'readonly' => 'readonly'
									)) }}
								</div>
							</div>

							<div class="form-group {{ $errors->has('nama') ? 'has-error' : '' }}">
								{{ Form::label('nama', 'Nama', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::text('nama', null, array(
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
									{{-- Using Form::password model doesn't work --}}
									{{-- Form::password('pass', array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('pass') ? 'tooltip' : '',
										'data-title' => $errors->has('pass') ? $errors->first('pass') : '',
										'placeholder' => 'Ketikkan Password Anda'
									)) --}}
									<input type="password" name="pass" id="pass" class="form-control has-tooltip" data-toggle="{{ $errors->has('pass') ? 'tooltip' : '' }}" data-title="{{ $errors->has('pass') ? $errors->first('pass') : '' }}" placeholder="Ketikkan Password Anda" value="{{ Input::old('pass', $user->pass) }}">
								</div>
							</div>

							<div class="form-group {{ $errors->has('pass_confirmation') ? 'has-error' : '' }}">
								{{ Form::label('pass_confirmation', 'Konfirmasi Password', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{-- Form::password('pass_confirmation', array(
										'class' => 'form-control has-tooltip',
										'data-toggle' => $errors->has('pass_confirmation') ? 'tooltip' : '',
										'data-title' => $errors->has('pass_confirmation') ? $errors->first('pass_confirmation') : '',
										'placeholder' => 'Ketikkan Konfirmasi Password Anda'
									)) --}}
									<input type="password" name="pass_confirmation" id="pass_confirmation" class="form-control has-tooltip" data-toggle="{{ $errors->has('pass_confirmation') ? 'tooltip' : '' }}" data-title="{{ $errors->has('pass_confirmation') ? $errors->first('pass_confirmation') : '' }}" placeholder="Ketikkan Password Anda" value="{{ Input::old('pass_confirmation', $user->pass) }}">
								</div>
							</div>

							<div class="form-group {{ $errors->has('tmp_lhr') ? 'has-error' : '' }}">
								{{ Form::label('tmp_lhr', 'Tempat Lahir', array(
									'class' => 'control-label col-md-4'
								)) }}
								<div class="col-md-8">
									{{ Form::text('tmp_lhr', null, array(
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
									{{ Form::text('tgl_lhr', Input::old('tgl_lhr', date('d-m-Y', strtotime($user->tgl_lhr))), array(
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
									{{ Form::select('gender', $gender, null, array(
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
									{{ Form::textarea('alamat', null, array(
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
									{{ Form::text('pekerjaan', null, array(
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
									{{ Form::text('no_hp', null, array(
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
									{{ Form::text('email', null, array(
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