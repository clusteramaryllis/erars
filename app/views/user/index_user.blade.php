@extends('layouts.cpanel')
@section('title','Indeks User Umum')
@section('body_class','cpanel create-user')

@section('bottom_css')
	@parent
	<link rel="stylesheet" href="{{ asset('packages/jgrowl/jquery.jgrowl.css') }}">	
	<link rel="stylesheet" href="{{ asset('assets/css/cpanel.css') }}">	
	<link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
@stop

@section('bottom_js')
	@parent
	<script src="{{ asset('packages/jgrowl/jquery.jgrowl.min.js') }}"></script>
	<script>
	jQuery(function($){
		
		@if(Session::has('success_message'))
		$.jGrowl("<i class='icon16 i-checkmark-3'></i> {{ Session::get('success_message') }}", {
			group: 'success',
			closeTemplate: '<i class="fa fa-close"></i>',
			animateOpen: {
				width: 'show',
				height: 'show',
			}
		});
		@endif

	});
	</script>
@stop

@section('content')
<div class="container" id="content">
  <div class="row">
  	<div class="col-md-12 clearfix">
	    <div class="btn-group">
	      <a href="#" class="btn btn-default"><i class="glyphicon glyphicon-home"></i></a>
	      <a href="#" class="btn btn-default">User</a>
	      <a href="#" class="btn btn-primary active">Umum</a>
	    </div>
	    <div class="pull-right">
	    	<a href="{{ action('UserController@getCreateUser') }}" class="btn btn-success">
	    		<i class="fa fa-plus"></i> Tambah User
	    	</a>
	    </div>
	    <hr>
  	</div>
	</div>

</div>
@stop