@extends('layouts.master')
@section('title','Login')
@section('body_class','login')

@section('bottom_css')
	@parent
	<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">	
@stop

@section('content')
<div class="container">

	<div class="row" style="margin-top:100px">
	    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3 login-box">
			{{ Form::open(array(
				'url' => 'login',
				'role' => 'form'
			)) }}
				<fieldset>
					<h2>Login Aplikasi</h2>
					<hr class="colorgraph">
					<div class="form-group">
						{{ Form::text('username', Input::old('username'), array(
							'placeholder' => 'Username',
							'class' => 'form-control input-lg'
						)) }}
					</div>
					<div class="form-group">
						{{ Form::password('password', array(
							'placeholder' => 'Password',
							'class' => 'form-control input-lg'
						)) }}
					</div>
					<hr class="colorgraph">

					{{-- error message --}}
					@if($errors->count())
					<div class="alert alert-danger-alt alert-dismissable">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<p>Anda mempunyai beberapa pesan error :</p>
						<ul>
						@foreach ($errors->all() as $message)
						<li>{{ $message }}</li>
						@endforeach
						</ul>
					</div>
					@endif
					{{-- end error message --}}
					
					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-12">
							{{ Form::button('Login', array(
								'type' => 'submit',
								'class' => 'btn btn-lg btn-success btn-block'
							)) }}
						</div>
						<div class="col-xs-12 col-sm-12 col-md-12">
							<hr>
							<p class="text-center text-muted">Copyright &copy; tofazakie</p>
						</div>
					</div>
				</fieldset>
			{{ Form::close() }}
		</div>
	</div>

</div>
@stop