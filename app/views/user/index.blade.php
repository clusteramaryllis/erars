@extends('layouts.cpanel')
@section('title','Indeks User')
@section('body_class','cpanel index')

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
	      <a href="#" class="btn btn-primary active">User</a>
	    </div>
	    <hr>
  	</div>
	</div>
	
	<div class="row">
		
		<div class="col-md-4">
			<div class="thumbnail">
				<a href="{{ action('UserController@getIndexERT') }}" class="icon-panel">
					<i class="fa fa-user-md"></i>
				</a>
				<div class="caption">
					<h3>
						<a href="{{ action('UserController@getIndexERT') }}" class="same">
							ERT
						</a>
					</h3>
				</div>
			</div>
		</div>

		<div class="col-md-4">
			<div class="thumbnail">
				<a href="{{ action('UserController@getIndexUser') }}" class="icon-panel">
					<i class="fa fa-user"></i>
				</a>
				<div class="caption">
					<h3>
						<a href="{{ action('UserController@getIndexUser') }}" class="same">
							Umum
						</a>
					</h3>
				</div>
			</div>
		</div>

	</div>
</div>
@stop