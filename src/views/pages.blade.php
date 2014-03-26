@extends('vessel::layout')

@section('content')

	<a href="{{ URL::route('vessel.pages.new') }}" class="btn btn-primary">New Page</a>

	<div class="clearfix mb-15"></div>
	
	@if($pages->isEmpty())

	<p>There aren't any pages yet.</p>

	@else

	<table class="table table-bordered">

		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>Title</th>
				<th>Updated</th>
				<th>Actions</th>
			</tr>
		</thead>

		<tbody>

			@foreach($pages as $page)

			<tr data-entity="pages-page-row" data-eid="{{ $page->id }}">
				<td><input type="checkbox"></td>
				<td>{{ str_repeat('&mdash;&nbsp;', $page->getLevel()) }}{{ link_to_route('vessel.pages.edit', $page->title, array($page->id)) }}</td>
				<td>{{ $page->updated_at->user() }}</td>
				<td>
					{{ link_to_route('vessel.pages.edit', 'Edit', array($page->id), array('class' => 'btn btn-xs btn-default')) }}
					<a href="#" class="btn btn-xs btn-danger">Delete</a>
				</td>
			</tr>

			@endforeach

		</tbody>

	</table>

	@endif

@stop