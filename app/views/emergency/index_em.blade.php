@extends('layouts.cpanel')
@section('title','Indeks Emergency')
@section('body_class','cpanel index-em')

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
				message: "Apa Anda yakin akan menghapus emergency dengan No ID " + name,
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
	      <a href="{{ action('HomeController@getIndex') }}" class="btn btn-default"><i class="glyphicon glyphicon-home"></i></a>
	      <a href="{{ action('EmergencyController@getIndex') }}" class="btn btn-default">Emergency</a>
	      <a href="#" class="btn btn-primary active">Kasus</a>
	    </div>
	    <div class="pull-right">
	    	<a href="{{ action('EmergencyController@getCreateEmergency') }}" class="btn btn-success">
	    		<i class="fa fa-plus"></i> Tambah Emergency
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
					{{ Form::label('q', 'Cari Deskripsi Emergency') }}
					{{ Form::text('q', Input::has('q') ? Input::get('q') : '', array(
						'class' => 'form-control'
					)) }}
				</div>

				<div class="form-group">
					{{ Form::label('filter_by', 'Filter Berdasarkan') }}
					{{ Form::select('filter_by', $filterBy, Input::has('filter_by') ? Input::get('filter_by') : '', array(
						'class' => 'form-control'
					)) }}
				</div>

				<div class="form-group">
					{{ Form::label('order_by', 'Pilih Order', array(
						'class' => 'sr-only'
					)) }}
					{{ Form::select('order_by', $orderBy, Input::has('order_by') ? Input::get('order_by') : '', array(
						'class' => 'form-control'
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
			@if($em_cases->count())
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Deskripsi</th>
							<th>Tipe</th>
							<th>Lokasi</th>
							<th>Waktu Kejadian</th>
							<th>Pelapor</th>
							<th>Validator</th>
							<th>Resolver</th>
							<th>Status</th>
							<th>Aksi</th>
						</tr>
					</thead>

					<?php // determine label color
					$opt_color = array(
						5 => 'active',
						4 => 'warning',
						3 => 'danger',
						2 => 'success',
						1 => 'info'
					);
					$status_color = array(
						0 => 'text-muted',
						1 => 'text-primary',
						2 => 'text-success'
					);
					?>

					<tbody>
						@foreach ($em_cases as $em_case)
						<tr>
							<td>{{ $em_case->desc }}</td>
							<td>{{ $em_case->em_type->type_name }}</td>
							<td>
								<p>Latitude : {{ $em_case->lat }}</p>
								<p>Longitude : {{ $em_case->lon }}</p>
							</td>
							<td>{{ date('d-m-Y H:i:s', strtotime($em_case->time)) }}</td>
							<td class="{{ $opt_color[$em_case->user_reporter->grup] }}">
								{{ $em_case->user_reporter->no_id }} - {{ $em_case->user_reporter->nama }}
							</td>
							<td class="{{ $opt_color[$em_case->user_validator->grup] }}">
								{{ $em_case->user_validator->no_id }} - {{ $em_case->user_validator->nama }}
							</td>
							@if ($em_case->user_resolver)
							<td class="{{ $opt_color[$em_case->user_resolver->grup] }}">
								{{ $em_case->user_resolver->no_id }} - {{ $em_case->user_resolver->nama }}
							</td>
							@else
							<td><i>Kosong</i></td>
							@endif
							<td>
								<span class="{{ $status_color[$em_case->status] }}">{{ $status[$em_case->status] }}</span>
							</td>
							<td width="17%">
								<a href="{{ action('EmergencyController@getEditEmergency', $em_case->case_id) }}" class="btn btn-info btn-sm">
									<i class="fa fa-edit"></i> Lihat / Edit
								</a>
								<a href="{{ action('EmergencyController@deleteDestroyEmergency', $em_case->case_id) }}" class="btn btn-danger btn-sm delete-action" data-name="{{ $em_case->case_id }}">
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
				{{ $em_cases->links() }}
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