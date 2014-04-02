@extends('vessel::layout')

@section('content')

	<a href="{{ URL::route('vessel.blocks.new') }}" class="btn btn-primary">New Block</a>

	<div class="clearfix mb-15"></div>
	
	@if($blocks->isEmpty())

	<p>There aren't any blocks yet.</p>

	@else

	<table class="table table-bordered">

		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>Title</th>
				<th>Slug</th>
				<th>Updated</th>
				<th>Actions</th>
			</tr>
		</thead>

		<tbody>

			@foreach($blocks as $block)

			<tr data-entity="blocks-block-row" data-eid="{{ $block->id }}">
				<td><input type="checkbox"></td>
				<td>{{ link_to_route('vessel.blocks.edit', $block->title, array($block->id)) }}</td>
				<td>{{ $block->slug }}</td>
				<td>{{ $block->updated_at->user() }}</td>
				<td>
					{{ link_to_route('vessel.blocks.edit', 'Edit', array($block->id), array('class' => 'btn btn-xs btn-default')) }}
					<a href="#" class="btn btn-xs btn-danger">Delete</a>
				</td>
			</tr>

			@endforeach

		</tbody>

	</table>

	@endif

@stop