@extends('vessel::layout')

@section('content')

	<a href="{{ URL::route('vessel.menus.new') }}" class="btn btn-primary">{{ t('menus.new-button') }}</a>

	<div class="clearfix mb-15"></div>

	@if ($menus->isEmpty())

	<p>{{ t('menus.no-menus') }}</p>

	@else
	
	<table class="table table-bordered">

		<thead>
			<tr>
				<th>{{ t('general.title') }}</th>
				<th>{{ t('general.slug') }}</th>
				<th>{{ t('general.updated') }}</th>
				<th>{{ t('general.actions') }}</th>
			</tr>
		</thead>

		<tbody>

			@foreach($menus as $menu)

			<tr>
				<td>{{ link_to_route('vessel.menus.edit', $menu->title, array($menu->id)) }}</td>
				<td>{{ $menu->slug }}</td>
				<td>{{ $menu->updated_at->user() }}</td>
				<td>
					{{ link_to_route('vessel.menus.edit', t('general.edit'), array($menu->id), array('class' => 'btn btn-xs btn-default')) }}
					{{ link_to_route('vessel.menus.delete', t('general.delete'), array($menu->id), array('class' => 'btn btn-xs btn-danger')) }}
				</td>
			</tr>

			@endforeach

		</tbody>

	</table>

	@endif

@stop