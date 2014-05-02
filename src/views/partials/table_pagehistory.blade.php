
@if($history->isEmpty())

<p>{{ t('general.there-are-none') }}</p>

@else

<table class="table table-responsive table-condensed table-page-history">
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th>{{ t('general.date') }}</th>
			<th>{{ t('general.user') }}</th>
			<th>{{ t('general.actions') }}</th>
		</tr>
	</thead>

	<tbody>

		@foreach($history as $pagehistory)

		<tr{{ (isset($current) && $current && $current->id == $pagehistory->id) ? ' class="success"' : '' }}>
			<td>{{ $pagehistory->edit }}</td>
			<td>{{ $pagehistory->created_at->user() }}</td>
			<td>{{ $pagehistory->user->username }}</td>
			<td>
				<a href="{{ URL::route('vessel.pages.edit', array('id' => $pagehistory->page->id, 'history' => $pagehistory->id)) }}" class="btn btn-xs btn-primary">{{ t('general.load' ) }}</a>
				<a href="{{ URL::route('vessel.pagehistory.delete', array('id' => $pagehistory->id)) }}" class="btn btn-xs btn-danger">{{ t('general.delete' ) }}</a>
			</td>
		</tr>

		@endforeach

	</tbody>
</table>

<a href="{{ URL::route('vessel.pagehistory.delete.all', array('id' => $pagehistory->page->id, 'type' => (($pagehistory->is_draft) ? 'drafts' : 'edits'))) }}" class="btn btn-danger mb-15">{{ t('general.delete-all' ) }}</a>

@endif
