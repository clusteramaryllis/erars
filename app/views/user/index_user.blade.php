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
			closeTemplate: '<i class="fa fa-times"></i>',
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

	<div class="row">
		<div class="col-md-12">
			<div class="well well-sm clearfix">
				{{ Form::open(array(
					'class' => 'form-inline form-inline-wide',
					'method' => 'get'
				)) }}

				<div class="form-group">
					{{ Form::label('q', 'Cari Nama') }}
					{{ Form::text('q', Input::has('q') ? Input::get('q') : '', array(
						'class' => 'form-control'
					)) }}
				</div>

				<div class="form-group">
					{{ Form::label('order_by', 'Urutkan berdasarkan', array(
						'class' => 'sr-only'
					)) }}
					{{ Form::select('order_by', $orderBy, Input::has('order_by') ? Input::get('order_by') : '', array(
						'class' => 'form-control'
					))}}
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
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>No ID</th>
						<th>Nama</th>
						<th>Alamat</th>
						<th>Pekerjaan</th>
						<th>No HP</th>
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($users as $user)
					<tr>
						<td>{{ $user->no_id }}</td>
						<td>{{ $user->nama }}</td>
						<td>{{ $user->alamat }}</td>
						<td>{{ $user->pekerjaan }}</td>
						<td>{{ $user->no_hp }}</td>
						<td>
							<a href="#" class="btn btn-info btn-sm">
								<i class="fa fa-edit"></i> Edit
							</a>
							<a href="#" class="btn btn-danger btn-sm">
								<i class="fa fa-trash-o"></i> Hapus
							</a>
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="centered">
				{{ $users->links() }}
			</div>
		</div>
	</div>

</div>
@stop