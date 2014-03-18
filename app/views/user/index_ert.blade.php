@extends('layouts.cpanel')
@section('title','Indeks User Umum')
@section('body_class','cpanel index-user')

@section('bottom_css')
	@parent
	<link rel="stylesheet" href="{{ asset('packages/jgrowl/jquery.jgrowl.css') }}">	
	<link rel="stylesheet" href="{{ asset('assets/css/cpanel.css') }}">	
	<link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
@stop

@section('bottom_js')
	@parent
	<script src="{{ asset('packages/jgrowl/jquery.jgrowl.min.js') }}"></script>
	<script src="{{ asset('packages/bootbox/bootbox.min.js') }}"></script>
	<script>
	jQuery(function($){
		
		@if(Session::has('success_message'))
		// success message
		$.jGrowl("<i class='icon16 i-checkmark-3'></i> {{ Session::get('success_message') }}", {
			group: 'success',
			closeTemplate: '<i class="fa fa-times"></i>',
			animateOpen: {
				width: 'show',
				height: 'show',
			}
		});
		@endif

		@if(Session::has('delete_message'))
		// delete message
		$.jGrowl("<i class='icon16 i-checkmark-3'></i> {{ Session::get('delete_message') }}", {
			group: 'error',
			closeTemplate: '<i class="fa fa-times"></i>',
			animateOpen: {
				width: 'show',
				height: 'show',
			}
		});
		@endif

		// disabled hash reference
		$('[href="#"]').on('click', function(e){
			e.preventDefault();
		});

		// delete action
		$('.delete-action').on('click',function(e){
			e.preventDefault();

			var name = $(this).attr('data-name'),
				link = $(this).attr('href');

			bootbox.dialog({
				title: "Konfirmasi",
				message: "Apa Anda yakin akan menghapus user " + name,
				buttons: {
					yes: {
						label: "Ya",
						className: "btn-danger",
						callback: function() {
							$('#delete-form').attr('action', link).trigger('submit');
						}
					},
					no: {
						label: "Tidak",
						className: "btn-default"
					}
				}
			});
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
	      <a href="#" class="btn btn-default">User</a>
	      <a href="#" class="btn btn-primary active">ERT</a>
	    </div>
	    <div class="pull-right">
	    	<a href="{{ action('UserController@getCreateERT') }}" class="btn btn-success">
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
					{{ Form::label('group_by', 'Pilih Grup', array(
						'class' => 'sr-only'
					)) }}
					{{ Form::select('group_by', $group, Input::has('group_by') ? Input::get('group_by') : '', array(
						'class' => 'form-control'
					))}}
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
			@if($users->count())
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>No ID</th>
							<th>Nama</th>
							<th>Alamat</th>
							<th>Grup</th>
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
							<td>{{ $group[$user->grup] }}</td>
							<td>{{ $user->no_hp }}</td>
							<td width="20%">
								<a href="{{ action('UserController@getEditUser', $user->no_id) }}" class="btn btn-info btn-sm">
									<i class="fa fa-edit"></i> Lihat / Edit
								</a>
								<a href="{{ action('UserController@deleteDestroyUser', $user->no_id) }}" class="btn btn-danger btn-sm delete-action" data-name="{{ $user->nama }}">
									<i class="fa fa-trash-o"></i> Hapus
								</a>
							</td>
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
				{{ $users->links() }}
			</div>
		</div>
	</div>

	{{-- Delete Form --}}
	{{ Form::open(array(
		'url' => '#',
		'id' => 'delete-form',
		'method' => 'delete'
	)) }}
	{{ Form::close() }}

</div>
@stop