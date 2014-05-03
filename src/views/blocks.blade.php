@extends('vessel::layout')

@section('content')

	<a href="{{ URL::route('vessel.blocks.new') }}" class="btn btn-primary">{{ t('blocks.new-button') }}</a>

	<div class="clearfix mb-15"></div>
	
	@if($blocks->isEmpty())

	<p>{{ t('blocks.no-blocks') }}</p>

	@else

	<table class="table table-bordered">

		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>{{ t('general.title') }}</th>
				<th>{{ t('general.slug') }}</th>
				<th>{{ t('general.updated') }}</th>
				<th>{{ t('general.actions') }}</th>
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
					{{ link_to_route('vessel.blocks.edit', t('general.edit'), array($block->id), array('class' => 'btn btn-xs btn-default')) }}
					{{ link_to_route('vessel.blocks.delete', t('general.delete'), array($block->id), array('class' => 'btn btn-xs btn-danger')) }}
				</td>
			</tr>

			@endforeach

		</tbody>

	</table>

	@endif

@stop