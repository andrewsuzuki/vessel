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
						{{ Form::text('title', null, array('class' => 'vessel-carry-field form-control input-lg', 'data-slugto' => '#slug', 'placeholder' => 'Title')) }}
					</div>
				</div>
				<div class="col-md-2 col-sm-3">
					<div class="form-group">
						{{ Form::select('formatter', \Hokeo\Vessel\Facades\Formatter::selectArray(), \Hokeo\Vessel\Facades\Formatter::formatter()->getName(), array('class' => 'vessel-carry-field vessel-select-formatter form-control input-lg')) }}
					</div>
				</div>
			</div>

			<div class="form-group">
				{{ $editor or '' }}
			</div>
			
			<div class="form-horizontal">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							{{ Form::label('slug', 'Slug', array('class' => 'col-sm-3 control-label')) }}
							<div class="col-sm-9">
								{{ Form::text('slug', null, array('class' => 'vessel-carry-field form-control input-sm')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('description', 'Description', array('class' => 'col-sm-3 control-label')) }}
							<div class="col-sm-9">
								{{ Form::text('description', null, array('class' => 'vessel-carry-field form-control input-sm')) }}
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							{{ Form::label('parent', 'Parent', array('class' => 'col-sm-3 control-label')) }}
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
							{{ Form::label('visible', 'Visibility', array('class' => 'col-sm-3 control-label')) }}
							<div class="col-sm-5 col-md-6">
								{{ Form::select('visible', array('1' => 'Public', '0' => 'Private'), null, array('class' => 'vessel-carry-field form-control input-sm')) }}
							</div>
							<div class="col-sm-4 col-md-3">
								<div class="checkbox" style="padding-top:5px">
									<label>
										{{ Form::checkbox('in_menu', '1', true) }} Menu
									</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('template', 'Template', array('class' => 'col-sm-3 control-label')) }}
							<div class="col-sm-9">
								{{ Form::select('template', $sub_templates, null, array('class' => 'vessel-carry-field form-control input-sm')) }}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="clearfix mb-15"></div>

			{{ Form::submit('Save', array('class' => 'btn btn-success mb-15')) }}
			<a href="#" class="btn btn-info mb-15">Preview</a>
			@if ($mode == 'edit')
			<a href="{{ URL::route('vessel.pages.delete', array('id' => $page->id)) }}" class="btn btn-danger mb-15">Delete</a>
			@if ($page->visible)
			<a href="{{ $page->url() }}" class="btn btn-default mb-15" target="_blank">View</a>
			@endif
			{{ Form::submit('Save As Draft', array('name' => 'save_as_draft', 'class' => 'btn btn-default mb-15')) }}
			{{ Form::button('Save As New', array('class' => 'btn btn-default mb-15', 'formaction' => URL::route('vessel.pages.new'), 'type' => 'submit', 'name' => 'duplicate', 'value' => 'true')) }}
			<a href="{{ URL::route('vessel.pages.edit', array('id' => $page->id)) }}" class="btn btn-default mb-15">Reload Current</a>
			@endif

		</div>

		@if ($mode == 'edit')

		<div class="clearfix mb-15"></div>

		<div class="col-md-6">
			<h3>Past Edits</h3>

			{{ View::make('vessel::partials.table_pagehistory')->with(['history' => $edits, 'current' => $pagehistory])->render() }}
		</div>

		<div class="col-md-6">
			<h3>Drafts</h3>

			{{ View::make('vessel::partials.table_pagehistory')->with(['history' => $drafts, 'current' => $pagehistory])->render() }}
		</div>

		@endif

	</div>

@stop