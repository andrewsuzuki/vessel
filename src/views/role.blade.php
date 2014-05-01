@extends('vessel::layout')

@section('content')

	<div class="row">
		
		<div class="col-lg-12">
			
			{{ Form::model($role, array('route' => array('vessel.users.roles.edit', (($mode == 'edit') ? $role->id : null)), 'role' => 'form')) }}
			
			<div class="form-horizontal">
				<div class="row">
					<div class="col-md-7 col-sm-8">
						<div class="form-group">
							{{ Form::label('name', 'Name', array('class' => 'col-sm-4 control-label')) }}

							<div class="col-sm-8">
								@if ($role_is_native)

								<p class="form-control-static">{{ $role->name }}</p>
								{{ Form::hidden('name', $role->name) }}
								@else

								{{ Form::text('name', null, array('class' => 'form-control input-sm')) }}
								@endif

							</div>
						</div>
						<div class="form-group">
							{{ Form::label('role_permissions', 'Permissions', array('class' => 'col-sm-4 control-label')) }}

							<div class="col-sm-8">

								@foreach ($permissions as $permission)

								<div class="checkbox"><label>{{ Form::checkbox('role_permissions[]', $permission->id, in_array($permission->id, $role_permissions)) }} {{ $permission->display_name }}</label></div>
								@endforeach

							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-4"></div>
							<div class="col-sm-8">
								<div class="clearfix mb-15"></div>
								{{ Form::submit('Save', array('class' => 'btn btn-success mb-15')) }}
								@if ($mode == 'edit')

								<a href="{{ URL::route('vessel.users.roles.delete', array('id' => $role->id)) }}" class="btn btn-danger mb-15">Delete</a>
								@endif

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

@stop