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
						{{ Form::text('title', null, array('class' => 'vessel-carry-field form-control input-lg', 'placeholder' => 'Title')) }}
					</div>
				</div>
				<div class="col-md-2 col-sm-3">
					<div class="form-group">
						{{ Form::select('formatter', \Hokeo\Vessel\FormatterFacade::selectArray(), \Hokeo\Vessel\FormatterFacade::formatter()->getName(), array('class' => 'vessel-carry-field vessel-select-formatter form-control input-lg')) }}
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
							{{ Form::label('visibility', 'Visibility', array('class' => 'col-sm-3 control-label')) }}
							<div class="col-sm-5 col-md-6">
								{{ Form::select('visibility', array('public' => 'Public', 'private' => 'Private'), null, array('class' => 'vessel-carry-field form-control input-sm')) }}
							</div>
							<div class="col-sm-4 col-md-3">
								<div class="checkbox" style="padding-top:5px">
									<label>
										{{ Form::checkbox('menu', '1', true) }} Menu
									</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('subtemplate', 'Template', array('class' => 'col-sm-3 control-label')) }}
							<div class="col-sm-9">
								{{ Form::select('subtemplate', array(), null, array('class' => 'vessel-carry-field form-control input-sm')) }}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="clearfix mb-15"></div>

			{{ Form::submit('Save', array('class' => 'btn btn-success mb-15')) }}
			<a href="#" class="btn btn-info mb-15">Preview</a>
			@if ($mode == 'edit')
			<a href="#" class="btn btn-danger mb-15">Delete</a>
			<a href="{{ $page->url() }}" class="btn btn-default mb-15" target="_blank">View</a>
			<a href="#" class="btn btn-default mb-15">Save As Draft</a>
			{{ Form::button('Save As New', array('class' => 'btn btn-default mb-15', 'formaction' => URL::route('vessel.pages.new'), 'type' => 'submit', 'name' => 'duplicate', 'value' => 'true')) }}
			@endif
		</div>
	</div>


@stop