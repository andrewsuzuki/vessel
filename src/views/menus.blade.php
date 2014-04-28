@extends('vessel::layout')

@section('content')

	<a href="{{ URL::route('vessel.menus.new') }}" class="btn btn-primary">New Menu</a>

	<div class="clearfix mb-15"></div>
	
	<table class="table table-bordered">

		<thead>
			<tr>
				<th>Title</th>
				<th>Slug</th>
				<th>Updated</th>
				<th>Actions</th>
			</tr>
		</thead>

		<tbody>

			@foreach($menus as $menu)

			<tr>
				<td>{{ link_to_route('vessel.menus.edit', $menu->title, array($menu->id)) }}</td>
				<td>{{ $menu->slug }}</td>
				<td>{{ $menu->updated_at->user() }}</td>
				<td>
					{{ link_to_route('vessel.menus.edit', 'Edit', array($menu->id), array('class' => 'btn btn-xs btn-default')) }}
					{{ link_to_route('vessel.menus.delete', 'Delete', array($menu->id), array('class' => 'btn btn-xs btn-danger')) }}
				</td>
			</tr>

			@endforeach

		</tbody>

	</table>

@stop