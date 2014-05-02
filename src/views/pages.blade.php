@extends('vessel::layout')

@section('content')

	<a href="{{ URL::route('vessel.pages.new') }}" class="btn btn-primary">{{ t('pages.new-button') }}</a>

	<div class="clearfix mb-15"></div>
	
	@if($pages->isEmpty())

	<p>{{ t('pages.no-pages') }}</p>

	@else

	<table class="table table-bordered">

		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>{{ t('general.title') }}</th>
				<th>{{ t('general.updated') }}</th>
				<th>{{ t('general.actions') }}</th>
			</tr>
		</thead>

		<tbody>

			@foreach($pages as $page)

			<tr data-entity="pages-page-row" data-eid="{{ $page->id }}">
				<td><input type="checkbox"></td>
				<td>{{ str_repeat('&mdash;&nbsp;', $page->getLevel()) }}{{ link_to_route('vessel.pages.edit', $page->title, array($page->id)) }}</td>
				<td>{{ $page->updated_at->user() }}</td>
				<td>
					{{ link_to_route('vessel.pages.edit', t('general.edit'), array($page->id), array('class' => 'btn btn-xs btn-default')) }}
					{{ link_to_route('vessel.pages.delete', t('general.delete'), array($page->id), array('class' => 'btn btn-xs btn-danger')) }}
				</td>
			</tr>

			@endforeach

		</tbody>

	</table>

	@endif

@stop