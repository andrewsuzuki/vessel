@extends('vessel::layout')

@section('content')

	<div class="row">
		
		<div class="col-lg-12">
			
			{{ Form::model($page, array('route' => array('vessel.pages.'.$mode, 'id' => $page->id), 'role' => 'form', 'class' => 'vessel-page-edit-form')) }}

			{{ Form::hidden('updated_at', null, array('class' => 'vessel-carry-field')) }}
			<input type="hidden" name="force_edit" class="vessel-carry-field" value="{{ Session::get('force_edit') }}">
			
			<div class="row">
				<div class="col-md-10 col-sm-9">
					<div class="form-group">
						{{ Form::text('title', null, array('class' => 'vessel-carry-field form-control input-lg', 'data-slugto' => '#slug', 'placeholder' => t('general.title'))) }}
					</div>
				</div>
				<div class="col-md-2 col-sm-3">
					<div class="form-group">
						{{ Form::select('formatter', $formatters_select_array, $formatter_current, array('class' => 'vessel-carry-field vessel-select-formatter form-control input-lg')) }}
					</div>
				</div>
			</div>

			<div class="form-group">
				{{ $interface or '' }}
			</div>
			
			<div class="form-horizontal">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							{{ Form::label('slug', t('general.slug'), array('class' => 'col-sm-3 control-label')) }}
							<div class="col-sm-9">
								{{ Form::text('slug', null, array('class' => 'vessel-carry-field form-control input-sm')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('description', t('general.description'), array('class' => 'col-sm-3 control-label')) }}
							<div class="col-sm-9">
								{{ Form::text('description', null, array('class' => 'vessel-carry-field form-control input-sm')) }}
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							{{ Form::label('parent', t('pages.parent'), array('class' => 'col-sm-3 control-label')) }}
							<div class="col-sm-5 col-md-6">
								{{ Form::selectPageParent('parent', $page, array('class' => 'vessel-carry-field form-control input-sm')) }}
							</div>
							<div class="col-sm-4 col-md-3">
								<div class="checkbox" style="padding-top:5px">
									<label>
										{{ Form::checkbox('nest_url', '1', true) }} Nest URL
									</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('visible', t('pages.visibility'), array('class' => 'col-sm-3 control-label')) }}
							<div class="col-sm-9">
								{{ Form::select('visible', array('1' => 'Public', '0' => 'Private'), null, array('class' => 'vessel-carry-field form-control input-sm')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('template', t('pages.template'), array('class' => 'col-sm-3 control-label')) }}
							<div class="col-sm-9">
								{{ Form::select('template', $sub_templates, null, array('class' => 'vessel-carry-field form-control input-sm')) }}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="clearfix mb-15"></div>

			{{ Form::submit(t('general.save'), array('class' => 'btn btn-success mb-15')) }}
			<a href="#" class="btn btn-info mb-15">{{ t('general.preview') }}</a>
			@if ($mode == 'edit')
			<a href="{{ URL::route('vessel.pages.delete', array('id' => $page->id)) }}" class="btn btn-danger mb-15">{{ t('general.delete') }}</a>
			@if ($page->visible)
			<a href="{{ $page->url() }}" class="btn btn-default mb-15" target="_blank">{{ t('general.view') }}</a>
			@endif
			{{ Form::submit(t('pages.save-as-draft'), array('name' => 'save_as_draft', 'class' => 'btn btn-default mb-15')) }}
			{{ Form::button(t('pages.save-as-new'), array('class' => 'btn btn-default mb-15', 'formaction' => URL::route('vessel.pages.new'), 'type' => 'submit', 'name' => 'duplicate', 'value' => 'true')) }}
			<a href="{{ URL::route('vessel.pages.edit', array('id' => $page->id)) }}" class="btn btn-default mb-15">{{ t('pages.reload-current') }}</a>
			@endif

		</div>

		@if ($mode == 'edit')

		<div class="clearfix mb-15"></div>

		<div class="col-md-6">
			<h3>{{ t('pages.past-edits') }}</h3>

			{{ View::make('vessel::partials.table_pagehistory')->with(['history' => $edits, 'current' => $pagehistory])->render() }}
		</div>

		<div class="col-md-6">
			<h3>{{ t('pages.drafts') }}</h3>

			{{ View::make('vessel::partials.table_pagehistory')->with(['history' => $drafts, 'current' => $pagehistory])->render() }}
		</div>

		@endif

	</div>

@stop