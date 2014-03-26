@extends('vessel::layout')

@section('content')

	<div class="row">
		
		<div class="col-lg-12">
			
			{{ Form::model($page, array('route' => array('vessel.pages.'.$mode, 'id' => $page->id)), array('role' => 'form')) }}

			{{ Form::hidden('updated_at') }}
			<input type="hidden" name="force_edit" value="{{ Session::get('force_edit') }}">
			
			<div class="row">
				<div class="col-md-10 col-sm-9">
					<div class="form-group">
						{{ Form::text('title', null, array('class' => 'form-control input-lg', 'placeholder' => 'Title')) }}
					</div>
				</div>
				<div class="col-md-2 col-sm-3">
					<div class="form-group">
						{{ Form::select('format', array('markdown', 'html'), null, array('class' => 'form-control input-lg')) }}
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
								{{ Form::text('slug', null, array('class' => 'form-control input-sm')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('description', 'Description', array('class' => 'col-sm-3 control-label')) }}
							<div class="col-sm-9">
								{{ Form::text('description', null, array('class' => 'form-control input-sm')) }}
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							{{ Form::label('parent', 'Parent', array('class' => 'col-sm-3 control-label')) }}
							<div class="col-sm-9">
								{{ Form::selectPageParent('parent', $page, array('class' => 'form-control input-sm')) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('subtemplate', 'Template', array('class' => 'col-sm-3 control-label')) }}
							<div class="col-sm-9">
								{{ Form::select('subtemplate', array(), null, array('class' => 'form-control input-sm')) }}
							</div>
						</div>

					</div>
				</div>
			</div>

			{{ Form::submit('Save', array('class' => 'btn btn-success mb-15')) }}
			<a href="#" class="btn btn-info mb-15">Preview</a>
			@if ($mode == 'edit')
			<a href="#" class="btn btn-danger mb-15">Delete</a>
			<a href="#" class="btn btn-default mb-15">Save As Draft</a>
			{{ Form::button('Save As New', array('class' => 'btn btn-default mb-15', 'formaction' => URL::route('vessel.pages.new'), 'type' => 'submit', 'name' => 'duplicate', 'value' => 'true')) }}
			@endif
		</div>
	</div>


@stop