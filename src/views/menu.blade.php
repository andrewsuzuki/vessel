@extends('vessel::layout')

@section('content')

	<div class="row">
		
		<div class="col-lg-12">
			
			{{ Form::model($menu, array('route' => array('vessel.menus.edit', 'id' => $menu->id), 'role' => 'form', 'class' => 'vessel-menu-edit-form')) }}

			{{ Form::hidden('updated_at', null) }}
			<input type="hidden" name="force_edit" value="{{ Session::get('force_edit') }}">
		
			<div class="row">
				<div class="col-sm-3">
					<div class="form-group">
						{{ Form::label('title', 'Title') }}
						{{ Form::text('title', null, array('class' => 'form-control input-sm', 'data-slugto' => '#slug')) }}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{{ Form::label('slug', 'Slug') }}
						{{ Form::text('slug', null, array('class' => 'form-control input-sm')) }}
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						{{ Form::label('description', 'Description') }}
						{{ Form::text('description', null, array('class' => 'form-control input-sm')) }}
					</div>
				</div>
			</div>

			<div class="form-group">

				<a href="#" class="btn btn-primary menu-add-item">Add item</a>
				
				<div class="dd">
					{{ $ddlist }}
				</div>
				<input type="hidden" name="menuitems" class="hidden-menu-serialized" value="">
			</div>

			<div class="clearfix mb-15"></div>

			{{ Form::submit('Save', array('class' => 'btn btn-success mb-15')) }}
			@if ($mode == 'edit')
			<a href="{{ URL::route('vessel.menus.delete', array('id' => $menu->id)) }}" class="btn btn-danger mb-15">Delete</a>
			@endif
		</div>

	</div>

@stop

@section('templates')

	<script id="vessel-menuitem-template" type="text/x-handlebars-template">
	<li class="dd-item" data-id="{@{{id}}}" data-type="{@{{type}}}" {@{{dataattrs}}}>
		<div class="dd-handle"></div>
		<div class="dd-content">
			{@{{title}}}&nbsp;&middot;&nbsp;<a href="#">Edit</a>&nbsp;&middot;&nbsp;<a href="#" class="menuitem-delete" style="color:red">Delete</a>
		</div>
	</li>
	</script>

	<script id="menuitem-alert-template" type="text/x-handlebars-template">

	<div class="modal fade" id="menuitem-alert" tabindex="-1" role="dialog" aria-labelledby="menuitem-alert-label" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="menuitem-alert-label">Edit Menu Item</h4>
				</div>
				<div class="modal-body">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#menuitem-edit-page" data-toggle="tab">Page</a></li>
						<li><a href="#menuitem-edit-link" data-toggle="tab">Link</a></li>
						<li><a href="#menuitem-edit-sep" data-toggle="tab">Separator</a></li>
					</ul>
					<div class="clearfix" style="height:15px"></div>
					<div class="tab-content">
						<div class="tab-pane active" id="menuitem-edit-page">
							<div class="form-group">
								<label for="menuitem-edit-title">Item Title</label>
								<input type="text" id="menuitem-edit-title" class="form-control">
							</div>
							<div class="form-group">
								<label for="menuitem-edit-page-input">Public Page</label>
								<select id="menuitem-edit-page-input" class="form-control">
									<option value="1">Page 1</option>
								</select>
							</div>
						</div>
						<div class="tab-pane" id="menuitem-edit-link">
							<div class="form-group">
								<label for="menuitem-edit-title">Item Title</label>
								<input type="text" id="menuitem-edit-title" class="form-control">
							</div>
							<div class="form-group">
								<label for="menuitem-edit-link-select">Link</label>
								<input type="text" id="menuitem-edit-link-input" class="form-control" placeholder="http://">
							</div>
						</div>
						<div class="tab-pane" id="menuitem-edit-sep">
							<p>A menu separator will be added.</p>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-success menuitem-alert-save" data-id="@{{mode}}">Save</button>
				</div>
			</div>
		</div>
	</div>

	</script>

@stop